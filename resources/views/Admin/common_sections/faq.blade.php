<x-app-layout>

   @php
      $breadCrumbs = [
          ['label' => 'WebPages', 'url' => 'dashboard', 'icon' => 'documentLined'],
          ['label' => 'Common Sections', 'url' => 'dashboard', 'icon' => 'globe'],
          ['label' => 'FAQs'],
      ];
   @endphp

   <x-indicators.breadcrumb :crumbs="$breadCrumbs" />
   <!-- ---------------- APP AREA Starts ---------------- -->

   <!-- Start Main Widgets -->
   <x-indicators.page_header_widget pageName="Common Section: Frequently Asked Quentions" />
   <!-- End Main Widgets -->


   <!-- Alert Component -->
   <x-indicators.alert-component message="Form Submission" />
   <!-- Alert Component -->

   @php
      $ACCORDION_1_VISIBILITY = 'hidden'; // hidden or ''
      $ACCORDION_2_VISIBILITY = '';
   @endphp
   <!---------------------------------------------------------------------------------------------- accordion -->
   <div class="hs-accordion-group">

      <x-ui_items.accordion.accordion-destroy />

      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-one">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify FAQ Head" />

         <div id="hs-destroy-collapse-one"
            class="hs-accordion-content w-full  {{ $ACCORDION_1_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-one">
            <div class="p-3">
               <!-- Form::Common Section-FAQ Details -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">
                     <form method="POST" action="{{ route('backend.common_section_store') }}"
                        class="p-2 rounded-lg dark:bg-neutral-700">
                        @csrf

                        <!-- IDENTIFIER INPUTS -->
                        <div class="hiddenInputs">
                           <input type="hidden" name="type" value="faq" readonly>
                           <input type="hidden" name="section" value="faq_head" readonly>
                           <input type="hidden" name="order_in_section" value="1" readonly>
                        </div>
                        <!-- IDENTIFIER INPUTS -->


                        <!-- CONTENT INPUTS -->
                        <div>
                           <div class="mt-2 mb-5">
                              <label for="title" class="inputLabelMedium">FAQ Head Title<span
                                    class="text-red-500">*</span></label>
                              <input type="text" name="title" class="mediumInput"
                                 value="{{ $faqHead?->title ?? '' }}">
                           </div>
                           {{--  --}}
                           <div class="mt-2 mb-5">
                              <label for="detail" class="inputLabelMedium">Details<span
                                    class="text-red-500">*</span></label>
                              <textarea class="summernote" name="detail" id="detail">{!! $faqHead?->detail ?? '' !!}</textarea>
                           </div>
                        </div>
                        <!-- CONTENT INPUTS -->

                        <button type="submit" class="my-4 saveButton">Submit/Update</button>
                     </form>
                  </div>
               </section>
               <!-- Form::Common Section-FAQ Details -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-one">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify FAQ Details" />

         <div id="hs-destroy-collapse-one"
            class="hs-accordion-content w-full  {{ $ACCORDION_2_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-one">
            <div class="p-3">
               <!-- Form::Common Section-FAQ Details -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">

                     <!-- CONTENT INPUTS -->
                     @for ($i = 1; $i <= 4; $i++)
                        <form method="POST" action="{{ route('backend.common_section_store') }}"
                           class="rounded-lg p-2 dark:bg-neutral-700">
                           @csrf

                           <!-- IDENTIFIER INPUTS -->
                           <div>
                              <div class="hiddenInputs">
                                 <input type="hidden" name="type" value="faq" readonly>
                                 <input type="hidden" name="section" value="faq_body" readonly>
                                 <input type="hidden" name="order_in_section" value="{{$i}}" readonly>
                              </div>
                           </div>
                           <!-- IDENTIFIER INPUTS -->


                           <div class="grid grid-cols-9 gap-2">

                              <div class="col-span-4 mb-5 mt-2 flex flex-col justify-center">
                                 <label for="title" class="inputLabelMedium">Title {{ $i }}<span
                                       class="text-red-500">*</span></label>

                                 <textarea class="summernote" name="title" id="title">{!! $faqBody->where('order_in_section', $i)->first()?->title ?? '' !!}</textarea>
                              </div>
                              <!--  -->
                              <div class="col-span-4 mb-5 mt-2">
                                 <label for="details" class="inputLabelMedium">Description<span
                                       class="text-red-500">*</span></label>
                                 <textarea class="summernote" name="detail" id="details">{!! $faqBody->where('order_in_section', $i)->first()?->detail ?? '' !!}</textarea>
                              </div>
                              {{--  --}}
                              <div class="cols-span-1 flex_xy_center">
                                 <button type="submit" class="saveButton my-4">Submit/Update</button>
                              </div>
                           </div>
                           <hr class="text-gray-300">
                        </form>
                     @endfor
                     <!-- CONTENT INPUTS -->

                  </div>
               </section>
               <!-- Form::Common Section-FAQ Details -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->

   </div>


   <!---------------------------------------------------------------------------------------------- accordion -->











   <!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>
