<?php

namespace App\Http\Controllers\Workflows\Lessons;

use App\Domains\Lessons\Actions\HistorySaveAction;
use App\Domains\Lessons\Events\HistoryNotifyEvent;
use App\Enums\{CRUDActionEnums, NotifyChannelEnums};
use App\Helpers\{Errors\ExceptionHandling, Ui\DatatableHelper, UidGenerator};
use App\Http\Controllers\Controller;
use App\Http\Requests\Workflows\LessonHistoryStoreRequest;
use App\Models\Workflows\Lessons\{Lesson, LessonGroup, LessonHistory};
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\View;
use Throwable;

class WebLessonHistoryController extends Controller
{
   public function index()
   {
      $search = trim((string)request()->input('search'));
      $perPage = 5;

      $allHistories = LessonHistory::with([
         'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher',
         'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Student',
         'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument',
         'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Teacher',
         'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Intrument',
         'scheduleRelatingBackTo_LessonGroup.groupRelationWith_Students.studentRelatingBackTo_User',
         'scheduleRelatingBackTo_Creator',
         'scheduleRelatingBackTo_Modifier',
      ])
         ->when($search !== '', function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
               $q->where('lesson_history_uid', 'like', "%{$search}%")
                  ->orWhere(function ($lessonHistoryQuery) use ($search) {
                     $lessonHistoryQuery->whereNotNull('lesson_id')
                        ->where('lesson_id', 'like', "%{$search}%");
                  })
                  ->orWhere(function ($lessonHistoryQuery) use ($search) {
                     $lessonHistoryQuery->whereNotNull('lesson_id')
                        ->whereHas('scheduleRelatingBackTo_Lesson', function ($lessonQuery) use ($search) {
                           $lessonQuery->where(function ($lessonSearchQuery) use ($search) {
                              $lessonSearchQuery->where('id', 'like', "%{$search}%")
                                 ->orWhere('lesson_uid', 'like', "%{$search}%")
                                 ->orWhere('title', 'like', "%{$search}%")
                                 ->orWhereHas('lessonRelatingBackTo_Intrument', function ($instrumentQuery) use ($search) {
                                    $instrumentQuery->where('name', 'like', "%{$search}%");
                                 })
                                 ->orWhereHas('lessonRelatingBackTo_Student', function ($studentQuery) use ($search) {
                                    $studentQuery->where('name', 'like', "%{$search}%");
                                 })
                                 ->orWhereHas('lessonRelatingBackTo_Teacher', function ($teacherQuery) use ($search) {
                                    $teacherQuery->where('name', 'like', "%{$search}%");
                                 });
                           });
                        });
                  })
                  ->orWhere(function ($groupHistoryQuery) use ($search) {
                     $groupHistoryQuery->whereNull('lesson_id')
                        ->whereNotNull('group_id')
                        ->where('group_id', 'like', "%{$search}%");
                  })
                  ->orWhere(function ($groupHistoryQuery) use ($search) {
                     $groupHistoryQuery->whereNull('lesson_id')
                        ->whereNotNull('group_id')
                        ->whereHas('scheduleRelatingBackTo_LessonGroup', function ($groupQuery) use ($search) {
                           $groupQuery->where(function ($groupSearchQuery) use ($search) {
                              $groupSearchQuery->where('id', 'like', "%{$search}%")
                                 ->orWhere('group_uid', 'like', "%{$search}%")
                                 ->orWhere('title', 'like', "%{$search}%")
                                 ->orWhereHas('groupRelatingBackTo_Intrument', function ($instrumentQuery) use ($search) {
                                    $instrumentQuery->where('name', 'like', "%{$search}%");
                                 })
                                 ->orWhereHas('groupRelatingBackTo_Teacher', function ($teacherQuery) use ($search) {
                                    $teacherQuery->where('name', 'like', "%{$search}%");
                                 });
                           });
                        });
                  });
            });
         })->orderByDesc('final_date')->orderBy('final_start_time')->get();

      $singleHistories = $allHistories->whereNotNull('lesson_id')->values();
      $groupHistories = $allHistories->whereNotNull('group_id')->values();

      $teachers = $allHistories->pluck('scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher')->merge($allHistories->pluck('scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Teacher'))->filter()->values();

      $students = $allHistories->pluck('scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Student')->merge($allHistories->flatMap(fn($h) => $h->scheduleRelatingBackTo_LessonGroup?->groupRelationWith_Students->pluck('studentRelatingBackTo_User') ?? []))->filter()->values();

      $instruments = $allHistories->pluck('scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument')->merge($allHistories->pluck('scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Intrument'))->filter()->values();

      $ReversedgroupedHistories = $allHistories->groupBy(fn($history) => optional($history->final_date)->format('Y-m-d'))->sortKeysDesc();

      $todayKey = now()->toDateString();
      $page = (int)request()->input('page', 0);

      if ($page < 1) {
         $todayGroupIndex = $ReversedgroupedHistories->keys()->search($todayKey);

         if ($todayGroupIndex === false && $ReversedgroupedHistories->isNotEmpty()) {
            $todayGroupIndex = $ReversedgroupedHistories
               ->keys()
               ->map(fn($date) => [
                  'date' => $date,
                  'distance' => abs(Carbon::parse($date)->diffInDays(Carbon::parse($todayKey), false)),
               ])
               ->sortBy('distance')
               ->keys()
               ->first();
         }

         $page = $todayGroupIndex === false ? 1 : (int)floor($todayGroupIndex / $perPage) + 1;
      }

      $groupedItems = $ReversedgroupedHistories->forPage($page, $perPage);

      $historiesPage = new LengthAwarePaginator(
         $groupedItems,
         $ReversedgroupedHistories->count(),
         $perPage,
         $page,
         [
            'path' => request()->url(),
            'query' => request()->query(),
         ]
      );

      return view('Admin.sidebar.Lessons.Histories.index', compact('allHistories', 'singleHistories', 'groupHistories', 'teachers', 'students', 'instruments', 'ReversedgroupedHistories', 'historiesPage', 'search'));
   }

   public function lessonIndex($lessonId)
   {
      try {
         $histories = LessonHistory::lessonId($lessonId)->get();
         $lesson = Lesson::findOrFail((int)$lessonId);
         $teacher = $lesson?->lessonRelatingBackTo_Teacher;
         $student = $lesson?->lessonRelatingBackTo_Student;

         return view('Admin.sidebar.Lessons.Histories.single_lesson', compact('histories', 'lesson', 'teacher', 'student'));
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }

   public function show(LessonHistory $history)
   {
      try {
         $history->load([
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Teacher',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Student',
            'scheduleRelatingBackTo_Lesson.lessonRelatingBackTo_Intrument',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Teacher',
            'scheduleRelatingBackTo_LessonGroup.groupRelatingBackTo_Intrument',
            'scheduleRelatingBackTo_LessonGroup.groupRelationWith_Students.studentRelatingBackTo_User',
            'scheduleRelatingBackTo_Creator',
            'scheduleRelatingBackTo_Modifier'
         ]);

         return view('Admin.sidebar.Lessons.Histories.show', compact('history'));
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }

   public function store(LessonHistoryStoreRequest $request, HistorySaveAction $action)
   {
      try {
         $type = $request->input('type');
         $validated = $request->validated();
         $USER = $request->user();

         $LESSON_HISTORY_UID = null;
         $existingHistory = null;
         $isNewRecord = false;

         $class = null;
         if ($type === 'lesson') {
            $class = Lesson::findOrFail((int)$validated['lesson_id']);
         } else {
            $class = LessonGroup::findOrFail((int)$validated['group_id']);
         }
         if ($class->starting_date && Carbon::parse($validated['final_date'])->isBefore($class->starting_date)) {

            ExceptionHandling::bailout(422, "The final date (" . Carbon::parse($validated['final_date'])->format('d-M-Y') . ") cannot be earlier than the lesson's starting date (" . Carbon::parse($class->starting_date)->format('d-M-Y') . ").");
         }

         // ------------ CHECKING EXISTANCE + UID ------------
         if (isset($validated['id']) && $validated['id']) {
            $existingHistory = LessonHistory::find($validated['id']);
            $LESSON_HISTORY_UID = $existingHistory->lesson_history_uid;
         } else {
            $LESSON_HISTORY_UID = UidGenerator::uniqueULID("HIST-" . strtoupper($type), 12, 18, 4);
            $isNewRecord = true;
         }
         // ------------ CHECKING EXISTANCE + UID ------------

         $history = $action->execute($validated, $existingHistory, $type, $class, $USER, $LESSON_HISTORY_UID, $isNewRecord);

         return redirect()->back()->with('success', "Lesson history '{$history->lesson_history_uid}' is saved successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }

   public function delete(Request $request, LessonHistory $history)
   {
      $name = null;
      try {
         $USER = $request->user();
         $lesson = $history?->scheduleRelatingBackTo_Lesson ?? null;
         $group = $history?->scheduleRelatingBackTo_LessonGroup ?? null;
         $teacher = $lesson ? $lesson->teacher_id : ($group ? $group->teacher_id : null);

         $studentIds = $group
            ? $group?->groupRelationWith_Students?->pluck('student_id')?->filter()?->map(fn($id) => (int)$id)?->toArray() ?? []
            : collect([$lesson?->student_id])->filter()->map(fn($id) => (int)$id)->toArray();

         $name = $lesson ? $lesson->title : ($group->title ?? null);
         $history->delete();

         // -------------------- NOTIFICATION EVENTS --------------------
         $actionFlag = CRUDActionEnums::DELETE;
         $targetUsers = array_values(array_unique(array_filter(array_merge([(int)$teacher], $studentIds))));
         event(new HistoryNotifyEvent(($history), null, $USER, $actionFlag, [NotifyChannelEnums::IN_APP], [NotifyChannelEnums::IN_APP], $targetUsers));
         // -------------------- NOTIFICATION EVENTS --------------------

         return redirect()->back()->with('success', "Lesson history '{$history->lesson_history_uid}' is deleted successfully.");
      } catch (Throwable $e) {
         return back()->with('error', 'Action error: ' . $e->getMessage())->withInput();
      }
   }
}
