<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;


class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $PaymentMethod = PaymentMethod::get();

        $Setting = Setting::forCurrentShop();
        return view('admin.setting.index', compact('PaymentMethod', 'Setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $section = $request->input('setting_section', 'general');
        $setting = Setting::findOrFail($id);

        if ($section === 'contact') {
            return $this->updateContactDetails($request, $setting);
        }

        $validator = Validator::make($request->all(), [
            'service_reminder_day' => 'required|integer|min:1',
            'service_default_duration' => 'required|integer|min:1',
            'booking_code_prefix' => ['required', 'string', 'regex:/^[A-Z0-9]{2,10}$/i'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ], 200);
        }

        $setting->service_reminder_day = $request->service_reminder_day;
        $setting->service_default_duration = $request->service_default_duration;
        $setting->booking_code_prefix = strtoupper($request->booking_code_prefix);
        $setting->invoice_send_sts = $request->has('invoice_mail_send_status') ? 1 : 0;
        $setting->save();

        return response()->json([
            'status' => 1,
            'message' => 'Setting Updated Successfully.'
        ]);
    }

    /**
     * Update website contact details used across the public site.
     */
    protected function updateContactDetails(Request $request, Setting $setting)
    {
        $validator = Validator::make($request->all(), [
            'footer_about' => 'nullable|string|max:2000',
            'contact_address' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:50',
            'contact_email' => 'required|email|max:150',
            'support_email' => 'nullable|email|max:150',
            'gstin' => ['nullable', 'string', 'regex:/^[0-9A-Z]{15}$/i'],
            'sac_code' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:30',
            'website_url' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'instagram_url' => 'nullable|string|max:255',
            'tiktok_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
            'twitter_url' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ], 200);
        }

        $nullableUrls = [
            'website_url', 'facebook_url', 'instagram_url', 'tiktok_url',
            'youtube_url', 'twitter_url', 'linkedin_url',
        ];
        foreach ($nullableUrls as $field) {
            if ($request->filled($field) === false) {
                $request->merge([$field => null]);
            }
        }

        $setting->fill([
            'footer_about' => $request->footer_about,
            'contact_address' => $request->contact_address,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'support_email' => $request->support_email ?: null,
            'gstin' => $request->gstin ? strtoupper($request->gstin) : null,
            'sac_code' => $request->sac_code ?: null,
            'whatsapp' => preg_replace('/\D+/', '', (string) $request->whatsapp),
            'website_url' => $request->website_url,
            'facebook_url' => $request->facebook_url,
            'instagram_url' => $request->instagram_url,
            'tiktok_url' => $request->tiktok_url,
            'youtube_url' => $request->youtube_url,
            'twitter_url' => $request->twitter_url,
            'linkedin_url' => $request->linkedin_url,
        ]);
        $setting->save();

        return response()->json([
            'status' => 1,
            'message' => 'Contact details updated successfully. Changes are live on the website.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
