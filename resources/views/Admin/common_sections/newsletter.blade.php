<x-app-layout>

   @php
      $breadCrumbs = [
         ['label' => 'WebPages', 'url' => 'dashboard', 'icon' => 'documentLined'],
         ['label' => 'Common Sections', 'url' => 'dashboard', 'icon' => 'globe'],
         ['label' => 'NewsLetter'],
      ];
   @endphp

   <x-indicators.breadcrumb :crumbs="$breadCrumbs" />
   <!-- ---------------- APP AREA Starts ---------------- -->

   <!-- Start Main Widgets -->
   <x-indicators.page_header_widget pageName="Common Section: Newsletter" />
   <!-- End Main Widgets -->


   <!-- Alert Component -->
   <x-indicators.alert-component message="Form Submission" />
   <!-- Alert Component -->

   @php
      $ACCORDION_1_VISIBILITY = ''; // hidden or ''
   @endphp
   <!---------------------------------------------------------------------------------------------- accordion -->
   <div class="hs-accordion-group">

      <x-ui_items.accordion.accordion-destroy />

      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-one">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Newsletter Form Texts" />

         <div id="hs-destroy-collapse-one"
            class="hs-accordion-content w-full  {{ $ACCORDION_1_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region" aria-labelledby="hs-destroy-heading-one">
            <div class="p-3">
               <!-- Form::Common Section-Newsletter Details -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">
                     <form method="POST" action="{{ route('backend.common_section_store') }}"
                        class="p-2 rounded-lg dark:bg-neutral-700">
                        @csrf

                        <!-- IDENTIFIER INPUTS -->
                        <div class="hiddenInputs">
                           <input type="hidden" name="type" value="newsletter" readonly>
                           <input type="hidden" name="section" value="newsletter" readonly>
                           <input type="hidden" name="order_in_section" value="1" readonly>
                        </div>
                        <!-- IDENTIFIER INPUTS -->


                        <!-- CONTENT INPUTS -->
                        <div>
                           <div class="mt-2 mb-5">
                              <label for="title" class="inputLabelMedium">Newsletter Title<span
                                    class="text-red-500">*</span></label>
                              <textarea class="summernote" name="title"
                                 id="title">{!! $newsletter?->title ?? '' !!}</textarea>
                           </div>
                           {{-- --}}
                           <div class="mt-2 mb-5">
                              <label for="detail" class="inputLabelMedium">Details<span
                                    class="text-red-500">*</span></label>
                              <textarea class="summernote" name="detail"
                                 id="detail">{!! $newsletter?->detail ?? '' !!}</textarea>
                           </div>
                        </div>
                        <!-- CONTENT INPUTS -->

                        <button type="submit" class="my-4 saveButton">Submit/Update</button>
                     </form>
                  </div>
               </section>
               <!-- Form::Common Section-Newsletter Details -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->

   </div>


   <!---------------------------------------------------------------------------------------------- accordion -->











   <!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>