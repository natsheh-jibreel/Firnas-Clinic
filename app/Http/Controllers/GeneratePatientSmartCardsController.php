<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\SmartPatientCards;
use App\Repositories\GeneratePatientSmartCardsRepository;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Setting;
use Flash;
use PDF;
use ArPHP\I18N\Arabic;

class GeneratePatientSmartCardsController extends AppBaseController
{

    private $GeneratePatientSmartCardsRepository;

    public function __construct(GeneratePatientSmartCardsRepository $staffRepo)
    {
        $this->GeneratePatientSmartCardsRepository = $staffRepo;
    }

    public function index()
    {
        $template = SmartPatientCards::pluck('template_name', 'id');
        $patient = Patient::where('template_id', null)->with('user')->get()->pluck('user.first_name', 'user.id');
        $logo = Setting::where('key', 'logo')->pluck('value');
        return view('generate_patient_smart_cards.index', compact('template', 'patient', 'logo'));
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $this->GeneratePatientSmartCardsRepository->store($input);

        Flash::success(__('messages.smart_patient_card.patient_smart_card_created'));

        if (isRole('doctor')) {
            return redirect(route('doctors.generate-patient-smart-cards.index'));
        }
        if (isRole('clinic_admin')) {
            return redirect(route('generate-patient-smart-cards.index'));
        }
    }

    public function destroy($id)
    {
        Patient::where('id', $id)->update(['template_id' => null, 'qr_code' => null]);

        return $this->sendSuccess(__('messages.smart_patient_card.patient_smart_card_deleted'));
    }

    public function cardDelail($id)
    {
        $data =  Patient::with('smartPatientCard', 'user', 'address')->where('id', $id)->first();
        $img = $data->profile;
        $clinic_name = Setting::where('key','clinic_name')->pluck('value')->first();
        $address_one = Setting::where('key','address_one')->pluck('value')->first();
        return response()->json(['data' => $data, 'img' => $img, 'clinic_name' => $clinic_name, 'address_one' => $address_one,]);
    }

    

    public function smartCardPdf($id)
    {
        // Fetch the data as before
        $datas = Patient::with('smartPatientCard', 'user', 'address')->findOrFail($id);
    
        $logo = Setting::where('key', 'logo')->pluck('value')->first();
        $clinic_name = Setting::where('key','clinic_name')->pluck('value')->first();
        $address_one = Setting::where('key','address_one')->pluck('value')->first();
    
        // Render the HTML content for the PDF
        $reportHtml = view('smart_card_pdf.smart_card_pdf', compact('datas', 'logo', 'clinic_name', 'address_one'))->render();
    
        // Use Ar-PHP to shape the Arabic text correctly
        $arabic = new Arabic();
        $p = $arabic->arIdentify($reportHtml);
    
        for ($i = count($p)-1; $i >= 0; $i -= 2) {
            $utf8ar = $arabic->utf8Glyphs(substr($reportHtml, $p[$i-1], $p[$i] - $p[$i-1]));
            $reportHtml = substr_replace($reportHtml, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
        }
    
        // Generate the PDF with the shaped Arabic text
        $pdf = PDF::loadHTML($reportHtml);
        return $pdf->download('PatientSmartCard.pdf');
    }



    public function cardQr($id)
    {
        $qr = Patient::findOrFail($id);
        $qrCode = QrCode::size(90)->generate(route('patient_show').'/'.$qr->patient_unique_id);
        return $qrCode;
    }
}
