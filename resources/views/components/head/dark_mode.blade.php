<style>
      :root {
         --transition-theme:
            background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }

      .no-transitions *,
      .no-transitions *::before,
      .no-transitions *::after {
         transition: none !important;
      }

      @media (prefers-reduced-motion: reduce) {
         * {
            transition: none !important;
         }
      }
   </style>