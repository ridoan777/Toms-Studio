<!----------- DARK MODE TRIGGER ---------->
<script>
   (function () {
      const savedTheme = localStorage.getItem('hs_theme');
      const html = document.documentElement;

      if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
         html.classList.add('dark');
         html.classList.remove('light');
      } else {
         html.classList.add('light');
         html.classList.remove('dark');
      }

      html.classList.add('no-transitions');
   })();
</script>
<!----------- DARK MODE TRIGGER ---------->

<!----------- PRELINE INITIALIZER ---------->
<script>
   document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
         if (window.HSStaticMethods && typeof HSStaticMethods.autoInit === 'function') {
            // HSStaticMethods.autoInit(['overlay'])  // not working in Laravel 13
            HSStaticMethods.autoInit()
         } else {
            console.warn('Preline overlay initialization failed: HSStaticMethods not found.')
         }
      }, 200)
   })
</script>
<!----------- PRELINE INITIALIZER ---------->

<!----------- DATATABLE ---------->
<script>
   // --------------------- ROW EVENTS (HOVER + CLICK) ---------------------
   window.dtApplyRowEvents = function (row, data) {
      $(row)
         .css('cursor', 'pointer')
         .attr('title', 'Click to view details')
         .on('click', function (e) {
            const $clickedCell = $(e.target).closest('td');
            if ($clickedCell.hasClass('no-row-click')) {
               return;
            }

            if ($(e.target).is('button, a, input, select, textarea')) {
               return;
            }

            const $interactiveParent = $(e.target).closest('button, a');
            if ($interactiveParent.length && $(row).find($interactiveParent).length) {
               return;
            }

            if (data.view_url) {
               window.location.href = data.view_url;
            }
         })
         .on('mouseenter', function () {
            $(this).css({ 'background-color': '#364153', 'color': '#fff' });
         })
         .on('mouseleave', function () {
            $(this).css({ 'background-color': '', 'color': '' });
         });
   };

   // --------------------- ROW EVENTS (NO-CLICKS) ---------------------
   $(document).on('click', '.row-check', function (e) {
      e.stopPropagation();
   });

   // --------------------- TOGGLE FORM (COMMON) ---------------------
   $(document).on('click', '.toggleForm button', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const form = $(this).closest('form');
      $.ajax({
         url: form.attr('action'),
         type: 'POST',
         data: form.serialize(),
         success: function (res) {
            if (!window.dtHandleActionResponse(res)) {
               return;
            }
            if (window.dataTableDisplay) {
               window.dataTableDisplay.ajax.reload(null, false);
            }
         },
         error: function (xhr) {
            let message = 'Error toggling status.';
            const res = xhr.responseJSON;
            if (res?.error) {
               message = res.error;
            } else {
               try {
                  const parsed = JSON.parse(xhr.responseText);
                  message = parsed?.error || parsed?.message || message;
               } catch (e) {
                  message = xhr.responseText || message;
               }
            }
            alert(message);
         }
      });
   });

   // --------------------- DELETE FORM (COMMON) ---------------------
   $(document).on('click', '.deleteForm button', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const error = $(this).data('confirm') || 'Are you sure you want to delete this?';

      if (confirm(error)) {
         $(this).closest('form').off('submit').submit();
      }
   });

   // --------------------- SELECT ALL CHECKBOX ---------------------
   $(document).on('change', '#select_all', function () {
      $('.row-check').prop('checked', $(this).is(':checked'));
   });
</script>
<script>
   // --------------------- HANDLE AJAX BUSINESS ERRORS ---------------------
   window.dtHandleActionResponse = function (res) {
      if (!res.success) {
         alert(res.error || 'Toggle failed');
         return false;
      }
      return true;
   };
</script>
<!----------- DATATABLE ---------->

<!----------- DARK MODE FLICKERING/WHITE FLASH FIX ---------->
<script>
   document.addEventListener('DOMContentLoaded', function () {
      const html = document.documentElement;
      const toggle = document.getElementById('darkSwtich');

      if (!toggle) return;

      toggle.checked = html.classList.contains('dark');

      setTimeout(() => {
         html.classList.remove('no-transitions');
      }, 100);

      // Toggle handler
      toggle.addEventListener('change', (e) => {
         if (e.target.checked) {
            html.classList.add('dark');
            localStorage.setItem('hs_theme', 'dark');
         } else {
            html.classList.remove('dark');
            localStorage.setItem('hs_theme', 'light');
         }
      });
   });
</script>
<!----------- DARK MODE FLICKERING/WHITE FLASH FIX ---------->

<!----------- MULTIPLE SELECT DROPDOWN ----------->

<script>
   // SOURCE: https://www.youtube.com/watch?v=fZgGc9XoZ6U   [Apr '22']
   var style = document.createElement('style');
   style.setAttribute("id", "multiselect_dropdown_styles");
   style.innerHTML = `
		.multiselect-dropdown{
         display: inline-block;
         padding: 6px 10px;
         border-radius: 8px;
         border: 1px solid #d1d5db;
         background-color: #fff;
         position: relative;
         transition: all 0.2s ease;
         cursor: pointer;

         background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
         background-repeat: no-repeat;
         background-position: right 0.75rem center;
         background-size: 16px;
      }
      .multiselect-dropdown:hover{
         border-color:#9ca3af;
      }
      .multiselect-dropdown:focus-within{
         border-color:#6366f1;
         box-shadow:0 0 0 2px rgba(99,102,241,0.15);
      }
		.multiselect-dropdown span.optext, .multiselect-dropdown span.placeholder{
			margin-right:0.5em; 
			margin-bottom:2px;
			padding:1px 0; 
			border-radius: 4px; 
			display:inline-block;
		}
		.multiselect-dropdown span.optext{
			padding:1px 0.75em;
         background:#eef2ff;
         color:#4338ca;
         padding:2px 10px;
         font-size:12px;
         border-radius:999px;
		}
		.multiselect-dropdown span.optext .optdel {
         margin-left:6px;
         font-size:10px;
         cursor:pointer;
         color:#6b7280;
      }
      .multiselect-dropdown span.optext .optdel:hover{
         color:#ef4444;
      }
		.multiselect-dropdown span.optext .optdel:hover { color: #c66;}
			.multiselect-dropdown span.placeholder{
			color:#ced4da;
		}
		.multiselect-dropdown-list-wrapper .multiselect-dropdown-search{
			margin-bottom:5px;
		}
      /* --------- To toggle if the dropdown would be absolute ot not --------- */
      /* use this on <select data-dropdown-position="absolute" or> */
      
      .multiselect-dropdown-list-wrapper{
         box-shadow:0 10px 25px rgba(0,0,0,0.08);
         z-index:9999;
         padding:6px;
         border-radius:10px;
         border:1px solid #e5e7eb;
         display:none;
         background:#fff;
      }
      .multiselect-dropdown-list-wrapper.dropdown-absolute{
         position:absolute;
         top:110%;
         left:0;
         right:0;
      }

      .multiselect-dropdown-list-wrapper.dropdown-static{
         position:static;
         margin-top:6px;
      }
      /* --------- To toggle if the dropdown'd be absolute ot not --------- */

		.multiselect-dropdown-list{
			padding:2px;
			height: 30rem;
			overflow-y:auto;
			overflow-x: hidden;
		}
		.multiselect-dropdown-list::-webkit-scrollbar {
			width: 6px;
		}
		.multiselect-dropdown-list::-webkit-scrollbar-thumb {
			background-color: #bec4ca;
			border-radius:3px;
		}

		.multiselect-dropdown-list div{
			padding: 5px;
		}
		.multiselect-dropdown-list input{
			height: 1.15em;
			width: 1.15em;
			margin-right: 0.35em;  
		}
		.multiselect-dropdown-list div.checked{
         background:#eef2ff;
		}
		.multiselect-dropdown-list div:hover{
			background:#f3f4f6;
         border-radius:6px;
		}
		.multiselect-dropdown span.maxselected {
			width:100%;
		}
		.multiselect-dropdown-all-selector {
			border-bottom:solid 1px #999;
		}
      /* ---------------- DARK MODE ---------------- */

      .dark .multiselect-dropdown{
         background:#1f2937;
         border-color:#374151;
         color:#e5e7eb;
      }

      .dark .multiselect-dropdown span.optext{
         background:#312e81;
         color:#c7d2fe;
      }

      .dark .multiselect-dropdown-list-wrapper{
         background:#1f2937;
         border-color:#374151;
      }

      .dark .multiselect-dropdown-list div{
         color:#e5e7eb;
      }

      .dark .multiselect-dropdown-list div:hover{
         background:#374151;
      }

      .dark .multiselect-dropdown-list div.checked{
         background:#312e81;
      }

      .dark .multiselect-dropdown span.placeholder{
         color:#6b7280;
      }

      /* SEARCH INPUT DARK */
      .dark .multiselect-dropdown-search{
         background:#111827;
         border:1px solid #374151;
         color:#e5e7eb;
      }

      .dark .multiselect-dropdown-search::placeholder{
         color:#6b7280;
      }
	`;
   document.head.appendChild(style);

   function MultiselectDropdown(options) {
      var config = {
         search: true,
         height: '24rem',
         placeholder: 'select',
         txtSelected: 'selected',
         txtAll: 'All',
         txtRemove: 'Remove',
         txtSearch: 'search',
         ...options
      };
      function newEl(tag, attrs) {
         var e = document.createElement(tag);
         if (attrs !== undefined) Object.keys(attrs).forEach(k => {
            if (k === 'class') { Array.isArray(attrs[k]) ? attrs[k].forEach(o => o !== '' ? e.classList.add(o) : 0) : (attrs[k] !== '' ? e.classList.add(attrs[k]) : 0) }
            else if (k === 'style') {
               Object.keys(attrs[k]).forEach(ks => {
                  e.style[ks] = attrs[k][ks];
               });
            }
            else if (k === 'text') { attrs[k] === '' ? e.innerHTML = '&nbsp;' : e.innerText = attrs[k] }
            else e[k] = attrs[k];
         });
         return e;
      }


      document.querySelectorAll("select[multiple]").forEach((el, k) => {
         // document.querySelectorAll(".dropdownSelectInputs").forEach((el, k) => {

         var div = newEl('div', { class: 'multiselect-dropdown', style: { width: config.style?.width ?? el.clientWidth + 'px', padding: config.style?.padding ?? '' } });
         el.style.display = 'none';
         el.parentNode.insertBefore(div, el.nextSibling);
         var listWrap = newEl('div', { class: 'multiselect-dropdown-list-wrapper' });

         var dropdownPosition = el.getAttribute('data-dropdown-position') || 'static';
         if (dropdownPosition === 'absolute') {
            listWrap.classList.add('dropdown-absolute');
         } else {
            listWrap.classList.add('dropdown-static');
         }


         var list = newEl('div', { class: 'multiselect-dropdown-list', style: { height: config.height } });
         var search = newEl('input', {
            class: ['multiselect-dropdown-search'].concat([config.searchInput?.class ?? 'form-control']),
            style: {
               width: '100%',
               display: el.attributes['multiselect-search']?.value === 'true' ? 'block' : 'none',
               padding: '8px 10px',
               border: '1px solid #e5e7eb',
               borderRadius: '8px',
               marginBottom: '8px',
               fontSize: '13px',
               outline: 'none',
               backgroundColor: '#ffffff',
               color: '#111827'
            },
            placeholder: 'Search...'
         });
         listWrap.appendChild(search);
         div.appendChild(listWrap);
         listWrap.appendChild(list);

         el.loadOptions = () => {
            list.innerHTML = '';

            if (el.attributes['multiselect-select-all']?.value == 'true') {
               var op = newEl('div', { class: 'multiselect-dropdown-all-selector' })
               var ic = newEl('input', { type: 'checkbox' });
               op.appendChild(ic);
               op.appendChild(newEl('label', { text: config.txtAll }));

               op.addEventListener('click', () => {
                  op.classList.toggle('checked');
                  op.querySelector("input").checked = !op.querySelector("input").checked;

                  var ch = op.querySelector("input").checked;
                  list.querySelectorAll(":scope > div:not(.multiselect-dropdown-all-selector)")
                     .forEach(i => { if (i.style.display !== 'none') { i.querySelector("input").checked = ch; i.optEl.selected = ch } });

                  el.dispatchEvent(new Event('change'));
               });
               ic.addEventListener('click', (ev) => {
                  ic.checked = !ic.checked;
               });

               list.appendChild(op);
            }

            Array.from(el.options).map(o => {
               var op = newEl('div', { class: o.selected ? 'checked' : '', optEl: o })
               var ic = newEl('input', { type: 'checkbox', checked: o.selected });
               op.appendChild(ic);
               op.appendChild(newEl('label', { text: o.text }));

               op.addEventListener('click', () => {
                  op.classList.toggle('checked');
                  op.querySelector("input").checked = !op.querySelector("input").checked;
                  op.optEl.selected = !!!op.optEl.selected;
                  el.dispatchEvent(new Event('change'));
               });
               ic.addEventListener('click', (ev) => {
                  ic.checked = !ic.checked;
               });
               o.listitemEl = op;
               list.appendChild(op);
            });
            div.listEl = listWrap;

            div.refresh = () => {
               div.querySelectorAll('span.optext, span.placeholder').forEach(t => div.removeChild(t));
               var sels = Array.from(el.selectedOptions);
               if (sels.length > (el.attributes['multiselect-max-items']?.value ?? 5)) {
                  div.appendChild(newEl('span', { class: ['optext', 'maxselected'], text: sels.length + ' ' + config.txtSelected }));
               }
               else {
                  sels.map(x => {
                     var c = newEl('span', { class: 'optext', text: x.text, srcOption: x });
                     if ((el.attributes['multiselect-hide-x']?.value !== 'true'))
                        c.appendChild(newEl('span', { class: 'optdel', text: '🗙', title: config.txtRemove, onclick: (ev) => { c.srcOption.listitemEl.dispatchEvent(new Event('click')); div.refresh(); ev.stopPropagation(); } }));

                     div.appendChild(c);
                  });
               }
               if (0 == el.selectedOptions.length) div.appendChild(newEl('span', { class: 'placeholder', text: el.attributes['placeholder']?.value ?? config.placeholder }));
            };
            div.refresh();
         }
         el.loadOptions();

         search.addEventListener('input', () => {
            list.querySelectorAll(":scope div:not(.multiselect-dropdown-all-selector)").forEach(d => {
               var txt = d.querySelector("label").innerText.toUpperCase();
               d.style.display = txt.includes(search.value.toUpperCase()) ? 'block' : 'none';
            });
         });

         div.addEventListener('click', () => {
            div.listEl.style.display = 'block';
            search.focus();
            search.select();
         });

         document.addEventListener('click', function (event) {
            if (!div.contains(event.target)) {
               listWrap.style.display = 'none';
               div.refresh();
            }
         });
      });
   }

   window.addEventListener('load', () => {
      MultiselectDropdown(window.MultiselectDropdownOptions);
   });
</script>
<!----------- MULTIPLE SELECT DROPDOWN ----------->

<!----------- SINGLE SELECT DROPDOWN ----------->
<script>
   function SingleSelectDropdown() {

      document.querySelectorAll(".singleSelectWithSearch:not([data-init])").forEach((el) => {
         el.setAttribute('data-init', 'true');

         const wrapper = document.createElement('div');
         wrapper.className = 'multiselect-dropdown';
         wrapper.style.width = '100%';

         const listWrap = document.createElement('div');
         const dropdownPosition = el.getAttribute('data-dropdown-position') || 'static';

         listWrap.className = 'multiselect-dropdown-list-wrapper';

         if (dropdownPosition === 'absolute') {
            listWrap.classList.add('dropdown-absolute');
         } else {
            listWrap.classList.add('dropdown-static');
         }

         const search = document.createElement('input');
         search.className = 'multiselect-dropdown-search';
         search.placeholder = 'Search...';
         search.style.width = '100%';
         search.style.padding = '8px 10px';
         search.style.border = '1px solid #e5e7eb';
         search.style.borderRadius = '8px';
         search.style.marginBottom = '8px';

         const list = document.createElement('div');
         list.className = 'multiselect-dropdown-list';

         el.style.display = 'none';
         el.parentNode.insertBefore(wrapper, el.nextSibling);

         wrapper.appendChild(listWrap);
         listWrap.appendChild(search);
         listWrap.appendChild(list);

         // load options
         function renderOptions() {
            list.innerHTML = '';

            Array.from(el.options).forEach(o => {
               const item = document.createElement('div');
               item.innerText = o.text;

               if (o.selected) item.classList.add('checked');

               item.addEventListener('click', () => {
                  Array.from(el.options).forEach(opt => opt.selected = false);
                  o.selected = true;

                  renderOptions();
                  refreshDisplay();

                  listWrap.style.display = 'none';
                  el.dispatchEvent(new Event('change'));
               });

               list.appendChild(item);
            });
         }

         function refreshDisplay() {
            wrapper.querySelectorAll('span').forEach(s => s.remove());

            const selected = el.selectedOptions[0];

            if (selected) {
               const tag = document.createElement('span');
               tag.className = 'optext';
               tag.innerText = selected.text;
               wrapper.appendChild(tag);
            } else {
               const placeholder = document.createElement('span');
               placeholder.className = 'placeholder';
               placeholder.innerText = 'Select';
               wrapper.appendChild(placeholder);
            }
         }

         // search
         search.addEventListener('input', () => {
            const val = search.value.toUpperCase();

            list.querySelectorAll("div").forEach(d => {
               d.style.display = d.innerText.toUpperCase().includes(val) ? 'block' : 'none';
            });
         });

         wrapper.addEventListener('click', () => {
            listWrap.style.display = 'block';
            search.focus();
         });

         document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
               listWrap.style.display = 'none';
            }
         });

         renderOptions();
         refreshDisplay();

      });
   }

   window.addEventListener('load', () => {
      SingleSelectDropdown();
   });
</script>
<!----------- SINGLE SELECT DROPDOWN ----------->

<!----------- SUMMERNOTE ----------->
<script>
   $('.summernote').summernote({
      placeholder: 'Type here...',
      tabsize: 2,
      height: null,
      minHeight: 120,
      maxHeight: 300,
      focus: true,
      toolbar: [
         ['style', ['style']],
         ['font', ['bold', 'underline', 'clear']],
         ['color', ['color']],
         ['para', ['ul', 'ol', 'paragraph']],
         ['table', ['table']],
         ['insert', ['link', 'picture', 'video']],  // 'video', 'math'  // needs katex.min.js
         ['view', ['fullscreen', 'codeview', 'help']]
      ]
   });
</script>
<!----------- SUMMERNOTE ----------->


<!----------- DROPIFY INITIALIZER ----------->
<script src="{{ asset('site_assets/js/dropify.min.js') }}"></script>

<script>
   $(document).ready(function () {
      $('.dropify').dropify();
   });

   // $('.dropify').on('dropify.afterClear', function (event, element) {
   //    document.getElementById('remove_image').value = '1';
   // });

   $('#avatarDropify').on('dropify.afterClear', function (event, element) {
      document.getElementById('remove_avatar').value = '1';
   });

   $('#imageDropify').on('dropify.afterClear', function (event, element) {
      document.getElementById('remove_image').value = '1';
   });

   // For video removal
   $('#videoDropify').on('dropify.afterClear', function (event, element) {
      document.getElementById('remove_video').value = '1';
   });
</script>

<script>
   $.ajaxSetup({
      headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
   });
</script>
<!----------- DROPIFY INITIALIZER ----------->

<!----------- FILEPOND CHUNK UPLOAD ----------->
<script>
   const errorDiv = document.getElementById('filepond-error');
   const submitBtn = document.getElementById('submitButton');

   FilePond.registerPlugin(FilePondPluginFileValidateSize, FilePondPluginFileValidateType);

   document.addEventListener('DOMContentLoaded', () => {
      let uploadingCount = 0;

      const updateSubmitBtn = () => {
         if (submitBtn) submitBtn.disabled = uploadingCount > 0;
      };

      document.querySelectorAll('.filepond').forEach(inputElement => {
         const isMultiple = inputElement.hasAttribute('multiple');

         const pond = FilePond.create(inputElement, {
            instantUpload: true,
            allowProcess: true,
            chunkUploads: true,
            chunkSize: 5000000,
            chunkRetryDelays: [2000, 5000, 10000],
            allowMultiple: isMultiple,
            server: {
               url: "{{ route('filepond_process') }}",
               headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
               },
            },
            acceptedFileTypes: ['video/mp4', 'video/avi', 'video/mov', 'video/mpeg', 'video/quicktime'],
            allowFileTypeValidation: true,
            maxFileSize: '1024MB',
            allowFileSizeValidation: true,
            labelMaxFileSizeExceeded: 'File is too large (max 1024MB or 1GB)',
            onerror: (error) => {
               errorDiv.textContent = (error && (error.body || error.main)) || 'An error occurred during upload.';
            },
         });

         // Using pond.on() instead of inline callbacks. These fire ONLY on user actions
         pond.on('addfile', (error, file) => {
            if (error) return; // validation error, not a real upload
            uploadingCount++;
            updateSubmitBtn();
         });

         pond.on('processfile', (error, file) => {
            uploadingCount = Math.max(0, uploadingCount - 1);
            updateSubmitBtn();
            if (error) {
               errorDiv.textContent = (error && (error.body || error.main)) || 'An error occurred during upload.';
            } else {
               errorDiv.textContent = '';
            }
         });

         pond.on('processfilerevert', (file) => {
            // file was removed after upload completed, no counter change needed
         });

         pond.on('removefile', (error, file) => {
            // Only decrement if the file was still uploading (not yet completed)
            if (file.status !== 5) { // 5 = PROCESSING_COMPLETE
               uploadingCount = Math.max(0, uploadingCount - 1);
               updateSubmitBtn();
            }
         });
      });
   });
</script>
<!----------- FILEPOND CHUNK UPLOAD ----------->

<!----------- FLOWBITE JS (No Flowbite CSS/Style bcz it will break the dashboard made with Preline) ----------->
<script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
<!----------- FLOWBITE JS ----------->