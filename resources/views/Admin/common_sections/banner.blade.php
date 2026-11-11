<x-app-layout>

   @php
      $breadCrumbs = [
          ['label' => 'WebPages', 'url' => 'dashboard', 'icon' => 'documentLined'],
          ['label' => 'Common Sections', 'url' => 'dashboard', 'icon' => 'globe'],
          ['label' => 'Banners'],
      ];
   @endphp

   <x-indicators.breadcrumb :crumbs="$breadCrumbs" />
   <!-- ---------------- APP AREA Starts ---------------- -->

   <!-- Start Main Widgets -->
   <x-indicators.page_header_widget pageName="Common Section: Banner" />
   <!-- End Main Widgets -->


   <!-- Alert Component -->
   <x-indicators.alert-component message="Form Submission" />
   <!-- Alert Component -->

   @php
      $ACCORDION_1_VISIBILITY = ''; // hidden or ''
      $ACCORDION_2_VISIBILITY = '';
   @endphp
   <!---------------------------------------------------------------------------------------------- accordion -->
   <div class="hs-accordion-group">

      <x-ui_items.accordion.accordion-destroy />

      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-one">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Banner Details" />

         <div id="hs-destroy-collapse-one"
            class="hs-accordion-content w-full  {{ $ACCORDION_1_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-one">
            <div class="p-3">
               <!-- Form::Common Section-Banner Details -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">
                     <form method="POST" action="{{ route('backend.common_section_store') }}"
                        class="p-2 rounded-lg dark:bg-neutral-700" enctype="multipart/form-data">
                        @csrf

                        <!-- IDENTIFIER INPUTS -->
                        <div class="hiddenInputs">
                           <input type="hidden" name="type" value="banner" readonly>

                           <select id="section" name="section"
                              class="py-2.5 px-3 block w-full rounded-xl border border-gray-300 bg-white text-gray-800 text-sm shadow-smfocus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-200 dark:focus:ring-blue-500">
                              <option value="about_us">About Us</option>
                              <option value="contact">Contact</option>
                           </select>


                           <input type="hidden" name="order_in_section" value="1" readonly>
                        </div>
                        <!-- IDENTIFIER INPUTS -->


                        <!-- CONTENT INPUTS -->
                        <div>
                           <div class="mt-2 mb-5">
                              <label for="image" class="inputLabelMedium">
                                 Banner Image <span class="redLabel">*</span>
                              </label>
                              <input id="image" name="images[]" type="file" class="dropify" data-height="150" />
                           </div>
                           {{--  --}}
                           <div class="mt-2 mb-5">
                              <label for="title" class="inputLabelMedium">Title<span
                                    class="text-red-500">*</span></label>
                              <textarea class="summernote" name="title" id="title">{!! $infoHead?->title ?? '' !!}</textarea>
                           </div>
                           {{--  --}}
                           <div class="mt-2 mb-5">
                              <label for="detail" class="inputLabelMedium">Details<span
                                    class="text-red-500">*</span></label>
                              <textarea class="summernote" name="detail" id="detail">{!! $infoHead?->detail ?? '' !!}</textarea>
                           </div>
                           {{--  --}}
                           <div class="mt-2 mb-5">
                              <label for="detail" class="inputLabelMedium">Button Text<span
                                    class="text-red-500">*</span></label>
                              <input type="text" name="btn_text" class="normalInput">
                           </div>
                        </div>
                        <!-- CONTENT INPUTS -->

                        <button type="submit" class="my-4 saveButton">Submit/Update</button>
                     </form>
                  </div>
               </section>
               <!-- Form::Common Section-Banner Details -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-six">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Display Outcome" />

         <div id="hs-destroy-collapse-six"
            class="hs-accordion-content w-full {{ $ACCORDION_2_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-six">

            <div class="p-3 grid grid-cols-1 gap-4">
               <!-- DISPLAY LIST -->
               <section class="my-10 sm:my-16 md:my-20 w-full mx-autogap-4">
                  <!-- Table Section -->
                  <div
                     class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                     <!-- Table -->
                     <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead class="bg-gray-50 dark:bg-neutral-800">
                           <tr>
                              <th scope="col" class="px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       S/N
                                    </span>
                                 </div>
                              </th>

                              <th scope="col" class="px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       Image
                                    </span>
                                 </div>
                              </th>

                              <th scope="col" class="px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       Section
                                    </span>
                                 </div>
                              </th>

                              <th scope="col" class="min-w-48 md:min-w-80 px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       Banner Title
                                    </span>
                                 </div>
                              </th>

                              <th scope="col" class="min-w-48 md:min-w-80 px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       Description
                                    </span>
                                 </div>
                              </th>

                              <th scope="col" class="px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       Button Text
                                    </span>
                                 </div>
                              </th>

                              <th scope="col" class="px-6 py-3 text-start">
                                 <div class="flex items-center gap-x-2">
                                    <span
                                       class="text-xs font-semibold uppercase text-gray-800 dark:text-neutral-200">
                                       Action
                                    </span>
                                 </div>
                              </th>

                           </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                           @foreach ($allBanners as $banner)
                              <tr>
                                 <td class="text-center">{{ $loop->iteration }}</td>
                                 <td class="size-px whitespace-nowrap">
                                    <div class="ps-6 py-3">
                                       @if (!empty($banner->image))
                                          <img src="{{ asset('storage/' . $banner->image ?? '') }}"
                                             alt="{{ $banner->image }}" class="!w-32 max-w-none">
                                       @else
                                          <img src="" alt="No Image" class="!w-32 max-w-none">
                                       @endif

                                    </div>
                                 </td>
                                 <td class="px-3 py-2 text-sm">{!! $banner->section ?? '' !!}</td>
                                 <td class="px-3 py-2 text-sm">{!! $banner->title ?? '' !!}</td>
                                 <td class="px-3 py-2 text-sm">{!! $banner->detail ?? '' !!}</td>
                                 <td class="px-3 py-2 text-sm text-center">{!! $banner->btn_text ?? '' !!}</td>
                                 <td class="text-center">
                                    <form action="{{ route('backend.common_section_delete', $banner->id ?? 0) }}"
                                       method="POST"
                                       onsubmit="return confirm('Are you sure you want to delete this banner?');">
                                       @csrf
                                       @method('DELETE')
                                       <button type="submit" class="deleteButton">
                                          {!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-3 h-3 text-gray-100']) !!}
                                       </button>
                                    </form>


                                 </td>
                              </tr>
                           @endforeach


                        </tbody>
                     </table>
                     <!-- End Table -->
                  </div>
                  <!-- End Table Section -->
               </section>
               <!--  -->
               <section
                  class="displayArea w-full">
               </section>
               <!-- DISPLAY LIST -->

            </div>
         </div>
      </div>

   </div>


   <!---------------------------------------------------------------------------------------------- accordion -->











   <!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>
