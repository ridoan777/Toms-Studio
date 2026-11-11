<?php

namespace App\Http\Controllers\Workflows\Positions;

use App\Helpers\ApiJsonReturnHelper;
use App\Http\Controllers\Controller;
use App\Helpers\Errors\ExceptionHandling;
use App\Models\Workflows\Instruments\Instrument;
use Throwable;


class ApiInstrumentController extends Controller
{
   public function index()
   {
      try {
         $instruments = Instrument::status(1)->get();

         return ApiJsonReturnHelper::handle(true, 200, "All instruments have been fetched successfully!", $instruments);
      } catch (Throwable $e) {
         return ExceptionHandling::handle('fetching instruments', $e);
      }
   }
   // -----------------------------------------------
}