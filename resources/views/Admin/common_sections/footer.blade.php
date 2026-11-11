<x-app-layout>

   @php
      $breadCrumbs = [
          ['label' => 'WebPages', 'url' => 'dashboard', 'icon' => 'documentLined'],
          ['label' => 'Common Sections', 'url' => 'dashboard', 'icon' => 'globe'],
          ['label' => 'Footer'],
      ];
   @endphp

   <x-indicators.breadcrumb :crumbs="$breadCrumbs" />
   <!-- ---------------- APP AREA Starts ---------------- -->

   <!-- Start Main Widgets -->
   <x-indicators.page_header_widget pageName="Common Section: Footer" />
   <!-- End Main Widgets -->


   <!-- Alert Component -->
   <x-indicators.alert-component message="Form Submission" />
   <!-- Alert Component -->

   @php
      $ACCORDION_1_VISIBILITY = 'hidden';
      $ACCORDION_2_VISIBILITY = 'hidden';
      $ACCORDION_3_VISIBILITY = 'hidden';
      $ACCORDION_4_VISIBILITY = 'hidden';
      $ACCORDION_5_VISIBILITY = '';
      $ACCORDION_6_VISIBILITY = 'hidden';
   @endphp
   <!---------------------------------------------------------------------------------------------- accordion -->
   <div class="hs-accordion-group">

      <x-ui_items.accordion.accordion-destroy />

      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-one">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Footer Details" />

         <div id="hs-destroy-collapse-one"
            class="hs-accordion-content w-full  {{ $ACCORDION_1_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-one">
            <div class="p-3">
               <!-- Form::Common Section-Footer Details -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">
                     <form method="POST" action="{{ route('backend.common_section_store') }}"
                        class="p-2 rounded-lg dark:bg-neutral-700" enctype="multipart/form-data">
                        @csrf

                        <!-- IDENTIFIER INPUTS -->
                        <div class="hiddenInputs">
                           <input type="hidden" name="type" value="footer" readonly>
                           <input type="hidden" name="section" value="detail" readonly>
                           <input type="hidden" name="order_in_section" value="1" readonly>
                        </div>
                        <!-- IDENTIFIER INPUTS -->


                        <!-- CONTENT INPUTS -->
                        <div>
                           <div class="mt-2 mb-5">
                              <label for="image" class="inputLabelMedium">
                                 Footer Image <span class="redLabel">*</span>
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
                        </div>
                        <!-- CONTENT INPUTS -->

                        <button type="submit" class="my-4 saveButton">Submit/Update</button>
                     </form>
                  </div>
               </section>
               <!-- Form::Common Section-Footer Details -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-two">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Social Links" />

         <div id="hs-destroy-collapse-two"
            class="hs-accordion-content w-full  {{ $ACCORDION_2_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-two">
            <div class="p-3">
               <!-- Form::Common Section-Hero -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">

                     @php
                        $socialMedia = ['Facebook', 'X', 'LinkedIn', 'Instagram'];
                     @endphp

                     @foreach ($socialMedia as $index => $item)
                        <form method="POST" action="{{ route('backend.common_section_store') }}"
                           class="p-2 rounded-lg dark:bg-neutral-700" enctype="multipart/form-data">
                           @csrf

                           <!-- IDENTIFIER INPUTS -->
                           <div>
                              <input type="hidden" name="type" value="footer">
                              <input type="hidden" name="section" value="social">
                              <input type="hidden" name="order_in_section" value="{{ $index + 1 }}">
                              <input type="hidden" name="title" value="{{ $item }}">
                           </div>
                           <!-- IDENTIFIER INPUTS -->

                           <!-- CONTENT INPUTS -->
                           <div class="mt-2 mb-5 flex_xy_center gap-4">
                              <label class="w-16 md:w-32 inputLabelMedium">{{ $item }}</label>
                              <input name="btn_url" type="url" class="normalInput" value="{{ $social[$index+1]?->btn_url ?? '' }}"/>
                              <button type="submit" class="saveButton">Save</button>
                           </div>
                           <!-- CONTENT INPUTS -->

                        </form>
                     @endforeach


                  </div>
               </section>
               <!-- Form -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-three">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Contact" />

         <div id="hs-destroy-collapse-three"
            class="hs-accordion-content w-full  {{ $ACCORDION_3_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-three">
            <div class="p-3">
               <!-- Form::Common Section-Hero -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">

                     @php
                        $socialMedia = ['Phone', 'Email', 'Address', 'Other'];
                     @endphp

                     @foreach ($socialMedia as $index => $item)
                        <form method="POST" action="{{ route('backend.common_section_store') }}"
                           class="p-2 rounded-lg dark:bg-neutral-700" enctype="multipart/form-data">
                           @csrf

                           <!-- IDENTIFIER INPUTS -->
                           <div>
                              <input type="hidden" name="type" value="footer">
                              <input type="hidden" name="section" value="contact">
                              <input type="hidden" name="order_in_section" value="{{ $index + 1 }}">
                              <input type="hidden" name="title" value="{{ $item }}">
                           </div>
                           <!-- IDENTIFIER INPUTS -->

                           <!-- CONTENT INPUTS -->
                           <div class="mt-2 mb-5 flex_xy_center gap-4">
                              <label class="w-16 md:w-32 inputLabelMedium">{{ $item }}</label>
                              <input name="title" type="hidden" class="normalInput" value="{{ $item }}"/>
                              <input name="subtitle" type="text" class="normalInput" value="{{ $contact[$index+1]->subtitle ?? '' }}"/>
                              <button type="submit" class="saveButton">Save</button>
                           </div>
                           <!-- CONTENT INPUTS -->

                        </form>
                     @endforeach


                  </div>
               </section>
               <!-- Form -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-four">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Quick Access Button" />

         <div id="hs-destroy-collapse-four"
            class="hs-accordion-content w-full  {{ $ACCORDION_4_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-four">
            <div class="p-3">
               <!-- Form::Common Section-Hero -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">

                     @for ($i = 1; $i <= 4; $i++)
                        <form method="POST" action="{{ route('backend.common_section_store') }}"
                           class="p-2 rounded-lg dark:bg-neutral-700" enctype="multipart/form-data">
                           @csrf

                           <!-- IDENTIFIER INPUTS -->
                           <div>
                              <input type="hidden" name="type" value="footer">
                              <input type="hidden" name="section" value="quick_access">
                              <input type="hidden" name="order_in_section" value="{{ $i }}">
                           </div>
                           <!-- IDENTIFIER INPUTS -->

                           <!-- CONTENT INPUTS -->
                           <div class="mt-2 mb-5 flex_xy_center gap-4">
                              <input name="title" type="text" class="normalInput w-1/3" value="{{ $quick_access[$i]->title ?? '' }}"/>
                              <input name="btn_url" type="url" class="normalInput" value="{{ $quick_access[$i]->btn_url ?? '' }}"/>
                              <button type="submit" class="saveButton">Save</button>
                           </div>
                           <!-- CONTENT INPUTS -->

                        </form>
                     @endfor


                  </div>
               </section>
               <!-- Form -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-five">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Modify Subscription Text" />

         <div id="hs-destroy-collapse-five"
            class="hs-accordion-content w-full  {{ $ACCORDION_5_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-five">
            <div class="p-3">
               <!-- Form::Common Section-Hero -->
               <section
                  class="displayArea my-5 p-4 rounded-lg border-none bg-white dark:bg-neutral-700 shadow-md shadow-gray-400 dark:shadow-none mx-auto">
                  <div class="my-5">

                        <form method="POST" action="{{ route('backend.common_section_store') }}"
                           class="p-2 rounded-lg dark:bg-neutral-700" enctype="multipart/form-data">
                           @csrf

                           <!-- IDENTIFIER INPUTS -->
                           <div>
                              <input type="hidden" name="type" value="footer">
                              <input type="hidden" name="section" value="subscribe_email">
                              <input type="hidden" name="order_in_section" value="1">
                           </div>
                           <!-- IDENTIFIER INPUTS -->

                           <!-- CONTENT INPUTS -->
                           <div class="mt-2 mb-5 flex_xy_center gap-4">
                              <input name="detail" type="text" class="mediumInput" value="{{ $subscribe_email->detail ?? '' }}"/>
                              <button type="submit" class="saveButton">Save</button>
                           </div>
                           <!-- CONTENT INPUTS -->

                        </form>

                  </div>
               </section>
               <!-- Form -->
            </div>
         </div>
      </div>
      <!-- ------------------ -->
      <div class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent"
         id="hs-destroy-heading-six">

         <x-ui_items.accordion.accordion_toggle_button toggleLabel="Display Outcome" />

         <div id="hs-destroy-collapse-six"
            class="hs-accordion-content w-full {{ $ACCORDION_6_VISIBILITY }} overflow-hidden transition-[height] duration-300"
            role="region"
            aria-labelledby="hs-destroy-heading-six">

            <div class="p-3 grid grid-cols-1 md:grid-cols-2 gap-4">
               <!-- DISPLAY LIST -->
               <section
                  class="displayArea">
                  <div class="my-10 sm:my-16 md:my-20 w-full sm:w-4/5 md:w-3/5 mx-auto flex flex-col gap-4">

                  </div>
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
