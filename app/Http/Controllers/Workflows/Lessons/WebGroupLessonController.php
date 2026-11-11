<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Domains\Lessons\Actions\{GroupLessonSaveAction, GroupStudentEnrollAction};
use App\Domains\Lessons\Enums\{LessonDifficultyEnums, LessonPhaseEnums};
use App\Domains\Lessons\Services\CascadeHistoryDeleteService;
use App\Enums\{CRUDActionEnums, NotifyChannelEnums};
use App\Domains\Lessons\Events\LessonNotifyEvent;
use App\Helpers\{Ui\DatatableHelper, UidGenerator};
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflows\GroupLessonStoreRequest;
use App\Models\Billings\Payments\PaymentPayable;
use App\Models\Users\User;
use App\Models\Workflows\Instruments\Instrument;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, GroupStudent, LessonHistory};
use Illuminate\Http\Request;
use Illuminate\Support\{Carbon};
use Illuminate\Support\Facades\{Log, Storage, DB};
use Exception;
use Throwable;

class WebGroupLessonController extends Controller
{
   public function create()
   {
      $users = User::whereHas('roles', function ($q) {
         $q->whereIn('role_key', ['teacher', 'student']);
      })->with('userRelatingBackTo_Instrument')->status(1)->get();

      $teachers = $users->filter(fn($u) => $u->hasRoleKey('teacher'))->values();
      $students = $users->filter(fn($u) => $u->hasRoleKey('student'))->values();
      $instrumentSubjects = Instrument::status(1)->get();

      $phases = LessonPhaseEnums::options();
      $difficulties = LessonDifficultyEnums::options();

      return view('Admin.sidebar.Lessons.Lesson_groups.create', compact('teachers', 'students', 'instrumentSubjects', 'phases', 'difficulties'));
   }


   public function index()
   {
      return view('Admin.sidebar.Lessons.Lesson_groups.index');
   }


   public function show(LessonGroup $lessonGroup)
   {
      $lessonGroup->load('groupRelationWith_Students');
      $existingStudentIds = $lessonGroup->groupRelationWith_Students->pluck('student_id')->toArray();

      $users = User::whereHas('roles', function ($q) {
         $q->whereIn('role_key', ['teacher', 'student']);
      })->with('userRelatingBackTo_Instrument')->status(1)->get();

      $teachers = $users->filter(fn($u) => $u->hasRoleKey('teacher'))->values();
      $students = $users->filter(fn($u) => $u->hasRoleKey('student'))->values();

      $availableStudents = $students->reject(fn($u) => in_array($u->id, $existingStudentIds))->values();
      $instrumentSubjects = Instrument::status(1)->get();

      $phases = LessonPhaseEnums::options();
      $difficulties = LessonDifficultyEnums::options();

      $lessonPayables = PaymentPayable::where('payable_type', LessonGroup::class)->where('payable_id', $lessonGroup->id)->latest('id')->get();

      $payableTotalRows = $lessonPayables->count();
      $payableTotalAmounts = $lessonPayables->sum('amount');
      $payableTotalPaidAmounts = $lessonPayables->whereNotNull('paid_at')->sum('amount');
      $payableTotalDues = $lessonPayables->whereNull('paid_at')->sum('amount');

      $lastPaidPayable = $lessonPayables->whereNotNull('paid_at')->sortByDesc('paid_at')->first();

      $payables = [
         'rows' => $payableTotalRows,
         'total' => $payableTotalAmounts,
         'paid' => $payableTotalPaidAmounts,
         'dues' => $payableTotalDues,
         'last_payment' => $lastPaidPayable,
      ];

      return view('Admin.sidebar.Lessons.Lesson_groups.show', compact('lessonGroup', 'teachers', 'students', 'availableStudents', 'instrumentSubjects', 'phases', 'difficulties', 'payables'));
   }


   public function dues(LessonGroup $lessonGroup)
   {
      $groupPayables = PaymentPayable::where('payable_type', LessonGroup::class)->where('payable_id', $lessonGroup->id)->latest('id')->paginate(20);

      return view('Admin.sidebar.Lessons.Lesson_groups.dues', compact('lessonGroup', 'groupPayables'));
   }


   public function store(GroupLessonStoreRequest $request, GroupLessonSaveAction $groupAction, GroupStudentEnrollAction $studentAction)
   {
      try {
         // dd($request->all());
         $validated = $request->validated();
         $USER = $request->user();
         $existingGroupLesson = null;
         $instrument = Instrument::findOrFail((int)$validated['subject_id']);
         $isNewRecord = false;
         $enrolledStudents = [];

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $existingGroupLesson = LessonGroup::find($validated['id']);
            $GROUP_UID = $existingGroupLesson->group_uid;
         } else {
            $GROUP_UID = UidGenerator::uniqueULID($validated['title'] ?? $instrument?->name, 12, 18, 4);
            $isNewRecord = true;
         }
         // ------------ CHECKING EXISTANCE + UID ------------

         $lessonGroup = $groupAction->execute($validated, $existingGroupLesson, $USER, $GROUP_UID, $isNewRecord);

         // ------------ STUDENT ENROLLEMNT ------------
         $students = $request->students ? array_values($request->students) : [];

         $incomingIds = collect($students)->pluck('id')->filter()->values()->toArray();
         GroupStudent::groupId($lessonGroup->id)->whereNotIn('student_id', $incomingIds)->delete();

         foreach ($students as $student) {
            $existingGroupStudent = null;
            $MEMBER_UID = null;
            $isNewRecord = false;

            if (empty($student['id']))
               continue;

            // ------------ CHECKING EXISTANCE + UID ------------
            $existingGroupStudent = GroupStudent::groupId($lessonGroup->id)->studentId($student['id'])->first();

            if ($existingGroupStudent) {
               $MEMBER_UID = $existingGroupStudent->member_uid;
            } else {
               $MEMBER_UID = UidGenerator::uniqueULID("MEMBER-" . $lessonGroup->title, 12, 18, 4);
               $isNewRecord = true;
            }
            // ------------ CHECKING EXISTANCE + UID ------------

            $enrolledStudents[] = $studentAction->execute($student, $lessonGroup, $existingGroupStudent, $USER, $MEMBER_UID, $isNewRecord);
         }
         // ------------ STUDENT ENROLLEMNT ------------


         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = empty($validated['id']) ? CRUDActionEnums::CREATE : CRUDActionEnums::UPDATE;

         $studentIds = collect($enrolledStudents)->pluck('student_id')->toArray();
         $targetUsers = [(int)$validated['teacher_id'], ...$studentIds];

         event(new LessonNotifyEvent($lessonGroup, null, $USER, $actionFlag, [NotifyChannelEnums::PUSH, NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP, NotifyChannelEnums::PUSH], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->back()->with('success', "Lesson group '{$lessonGroup->title}' is saved successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }


   public function toggle(Request $request, LessonGroup $lessonGroup)
   {
      try {
         $lessonGroup->update([
            'status' => !$lessonGroup->status,
         ]);

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = $lessonGroup->status ? CRUDActionEnums::ACTIVATE : CRUDActionEnums::DEACTIVATE;
         $studentIds = $lessonGroup->groupRelationWith_Students->pluck('student_id')->filter()->map(fn($id) => (int)$id)->toArray();

         $targetUsers = array_values(array_unique(array_filter(array_merge([(int)$lessonGroup?->teacher_id], $studentIds))));
         event(new LessonNotifyEvent($lessonGroup, null, $request->user(), $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return response()->json(['success' => true]);
      } catch (Exception $e) {
         return response()->json(['success' => false, 'error' => $e->getMessage()]);
      }
   }


   public function delete(Request $request, LessonGroup $lessonGroup, CascadeHistoryDeleteService $cascadeHistoryDeleteService)
   {
      $name = null;
      try {
         $USER = $request->user();
         $object = $lessonGroup;
         $studentIds = $object->groupRelationWith_Students->pluck('student_id')->filter()->map(fn($id) => (int)$id)->toArray();

         $teacher = $lessonGroup?->teacher_id ?? null;

         $name = $lessonGroup?->title ?? null;
         $cascadeHistoryDeleteService->handle($lessonGroup, 'group');
         $lessonGroup->delete();

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = CRUDActionEnums::DELETE;
         $targetUsers = array_values(array_unique(array_filter(array_merge([(int)$teacher], $studentIds))));
         event(new LessonNotifyEvent($lessonGroup, null, $USER, $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->route('backend_group_lesson_index')->with('success', "A group-lesson '{$name}' has been deleted successfully.");
      } catch (Exception $e) {
         return redirect()->back()->with('error', 'Action Error: ' . $e->getMessage())->withInput();
      }
   }


   public function bulkDelete(Request $request, CascadeHistoryDeleteService $cascadeHistoryDeleteService)
   {
      $request->validate([
         'ids' => 'required|array',
         'ids.*' => 'integer|exists:lesson_groups,id',
      ]);

      $ids = $request->ids;

      /** @var \Illuminate\Database\Eloquent\Collection<int, LessonGroup> $groupLessons */

      $groupLessons = LessonGroup::whereIn('id', $ids)->get();

      if ($groupLessons->isEmpty()) {
         return back()->with('error', 'No valid lesson groups selected.');
      }

      $MESSAGE = "Selected lessons are deleted.";
      $DISK_FOLDER = config('filesystems.default');
      $SKIPPED = [];
      $deleted = [];

      DB::beginTransaction();

      try {
         foreach ($groupLessons as $lesson) {
            /** @var LessonGroup $lesson */

            if (strtolower($lesson->phase) === 'disputed') {
               $SKIPPED[] = $lesson->title;
               continue;
            }

            // -------------------- NOTIFICATION EVENTS --------------------
            $actionFlag = CRUDActionEnums::DELETE;
            $targetUsers = [(int)$lesson?->teacher_id, (int)$lesson?->student_id];

            event(new LessonNotifyEvent($lesson, null, $request->user(), $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
            // -------------------- NOTIFICATION EVENTS --------------------

            $deleted[] = $lesson->title;

            $cascadeHistoryDeleteService->handle($lesson, 'group');
            $lesson->delete();

         }

         DB::commit();

         $MESSAGE = $MESSAGE . "success: [" . count($deleted) . "], dispute-skipped: [" . count($SKIPPED) . "].";

         return redirect()->back()->with('success', $MESSAGE);
      } catch (Throwable $e) {
         DB::rollBack();

         Log::error('Lessons bulk delete failed', ['ids' => $ids, 'error' => $e->getMessage()]);

         return back()->with('error', 'Bulk delete failed. No changes were made.');
      }
   }


   public function groupLessonDatatable(Request $request)
   {
      try {
         $columns = ['id', 'group_uid', 'title', 'phase', 'schedule_day', 'start_time', 'end_time', 'last_class', 'status', 'created_at'];

         $draw = (int)$request->input('draw');
         $start = (int)$request->input('start', 0);
         $length = (int)$request->input('length', 25);
         $orderColIndex = (int)$request->input('order.0.column', 0);
         $orderCol = $columns[$orderColIndex] ?? 'id';
         $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

         $query = LessonGroup::with(['groupRelatingBackTo_Teacher', 'groupRelatingBackTo_Intrument'])
            ->withCount('groupRelationWith_Students')
            ->select(['id', 'group_uid', 'teacher_id', 'subject_id', 'title', 'schedule_day', 'starting_date', 'start_time', 'end_time', 'status', 'created_at']);

         if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
               foreach (['group_uid', 'title', 'schedule_day', 'remarks'] as $field) {
                  $q->orWhere($field, 'like', "%{$search}%");
               }
               $q->orWhereHas(
                  'groupRelatingBackTo_Teacher',
                  fn($q) =>
                  $q->where(
                     fn($sub) =>
                     collect(['user_uid', 'username', 'name', 'email'])->each(fn($f) => $sub->orWhere($f, 'like', "%{$search}%"))
                  )
               );
               $q->orWhereHas(
                  'groupRelatingBackTo_Intrument',
                  fn($q) =>
                  $q->where('name', 'like', "%{$search}%")
               );
            });
         }

         $recordsTotal = LessonGroup::count();
         $recordsFiltered = $query->count();

         $data = $query->orderBy($orderCol, $orderDir)->offset($start)->limit($length)->get();

         $studentCounts = GroupStudent::whereIn('group_id', $data->pluck('id'))
            ->select('group_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('group_id')
            ->pluck('total', 'group_id');

         $sn = $start;
         $zones = \App\Helpers\Ui\ConvertDynamicTimezone::handle();

         $data->transform(function ($row) use (&$sn, $zones, $studentCounts) {
            $row->DT_RowId = 'row_' . $row->id;
            $row->view_url = route('backend_group_lesson_show', $row->id);
            $row->checkbox = '<input type="checkbox" name="ids[]" value="' . $row->id . '" class="row-check w-4 h-4 rounded border-gray-400 cursor-pointer checked:bg-blue-600 checked:border-blue-600">';
            $row->SN = ++$sn;

            $row->phase_subject = ($row->groupRelatingBackTo_Intrument?->name ?? 'N/A');

            $avgFee = GroupStudent::groupId($row->id)->avg('fees');
            if ($avgFee !== null) {
               $avgFee = '$' . number_format($avgFee / 100, 2);
            } else {
               $avgFee = 'N/A';
            }

            $row->enrolled = '<div class="flex flex-col gap-2">
               <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded">'
               . ($studentCounts[$row->id] ?? 0) . ' Students</span>'
               . ' <span class="inline-block text-xs text-gray-400"> Avg Fee:' . ($avgFee) . '</span>
               </div>';

            $row->teacher = DatatableHelper::userBlock($row->groupRelatingBackTo_Teacher);

            $days = is_array($row->schedule_day) ? implode(', ', $row->schedule_day) : ($row->schedule_day ?? 'N/A');
            $row->schedule_details = $days . '<br>' . ($row->start_time ? Carbon::parse($row->start_time)->format('H:i A') : 'N/A') . ' - ' . ($row->end_time ? Carbon::parse($row->end_time)->format('H:i A') : 'N/A');

            $zone = in_array($zones['zone'] ?? null, timezone_identifiers_list(), true) ? $zones['zone'] : 'Australia/Brisbane';

            $row->dates = ($row->starting_date ? $row->starting_date->timezone($zone)->format('d M Y') : 'N/A') . '<br>' . Carbon::now($zone)->startOfWeek(Carbon::MONDAY)->format('d M Y');

            $lastHistory = LessonHistory::groupId($row->id)->latest('final_date')->first();

            $row->status_label = DatatableHelper::phaseColor($row->status);

            $row->created_at_formatted = $row->created_at
               ? Carbon::createFromFormat('Y-m-d H:i:s', $row->getRawOriginal('created_at'), 'UTC')->timezone($zones['zone'])
                  ->format('d M Y, H:i') . ' (UTC' . $zones['offset'] . ')'
               : 'N/A';

            $row->actions = DatatableHelper::datatableAction($row, true, 'backend_group_lesson_toggle', 'backend_group_lesson_delete');

            return $row;
         });

         return DatatableHelper::handleDatatableSuccess($draw, $recordsTotal, $recordsFiltered, $data);

      } catch (Exception $e) {
         return DatatableHelper::handleDatatableError($request, $e, true, "Group Lesson Datatable Error");
      }
   }
}
