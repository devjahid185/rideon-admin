<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSmsNotification;
use App\Models\Module;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmailController extends Controller
{
    public function template(Request $request, $id)
    {
        abort_if(Gate::denies('email_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $module = Module::where('default_module', '1')->first();
        $moduleId = $module->id;
        $moduleName = $module->name;
        $emailTemplates = [
            'User Registration',
            'Signup OTP',
            'Forgot Password OTP',
            'Booking Cancellation by Guest',
            'Booking Cancellation/Confirmed by Vendor',
            'Payout Request',
            'Payment Sent',
            'Wallet Transaction',
        ];

        $AllEmailRecord = EmailSmsNotification::with([
            'notificationMapping' => function ($query) use ($moduleId) {
                $query->where('module', $moduleId)->with('emailType');
            },
        ])->where('status', 1)->get();

        $emaildata = EmailSmsNotification::where('id', $id)->first();

        if (is_null($emaildata)) {
            abort(404, 'Email template not found.');
        }

        return view('admin.email.index', compact('emaildata', 'AllEmailRecord'));
    }

    public function templatecreate(Request $request, $id)
    {
        if (Gate::denies('email_update')) {
            return redirect()->back()->with('error', "You don't have permission to perform this action.");
        }

        if ($request->type == 'vendor') {

            $emaildata = EmailSmsNotification::where('id', $id)->firstOrNew();

            $emailEnabled = $request->has('vendoremailsent') ? '1' : '0';
            $messageEnabled = $request->has('vendorsmssent') ? '1' : '0';
            $pushEnabled = $request->has('vendorpushsent') ? '1' : '0';
            $body = html_entity_decode($request->input('vendorbody'));

            $emaildata->fill([

                'vendorsubject' => $request->vendorsubject,
                'vendorbody' => $body,
                'vendorpush_notification' => $request->vendorpush_notification,
                'vendoremailsent' => $emailEnabled,
                'vendorsmssent' => $messageEnabled,
                'vendorpushsent' => $pushEnabled,
                'vendorsms' => $request->vendorsms,
            ]);

            $emaildata->save();

            return redirect()->route('vendor.email-templates', $id)->with('success', 'Updated successfully!');

            // return redirect()->route('vendor.email-templates', $id);
        }
        if ($request->type == 'admin') {

            $emaildata = EmailSmsNotification::where('id', $id)->firstOrNew();

            $emailEnabled = $request->has('adminemailsent') ? '1' : '0';
            $body = html_entity_decode($request->input('adminbody'));

            $emaildata->fill([

                'adminsubject' => $request->adminsubject,
                'adminemailsent' => $emailEnabled,
                'adminbody' => $body,

            ]);

            $emaildata->save();

            return redirect()->route('admin.email-templates', $id)->with('success', 'Updated successfully!');
        }
        $emaildata = EmailSmsNotification::where('id', $id)->firstOrNew();

        $emailEnabled = $request->has('emailsent') ? '1' : '0';
        $messageEnabled = $request->has('smssent') ? '1' : '0';
        $pushEnabled = $request->has('pushsent') ? '1' : '0';
        $status = 1;
        $link_text = 'abc';
        $lang = 'en';
        $lang_id = 1;
        $body = html_entity_decode($request->input('body'));

        // Update or create the record
        $emaildata->fill([
            'subject' => $request->subject,
            'body' => $body,
            'link_text' => $link_text,
            'lang' => $lang,
            'lang_id' => $lang_id,
            'sms' => $request->sms,
            'push_notification' => $request->push_notification,
            'emailsent' => $emailEnabled,
            'smssent' => $messageEnabled,
            'pushsent' => $pushEnabled,
            'status' => $status,
        ]);
        $emaildata->save();

        return redirect()->route('user.email-templates', $id)->with('success', 'Updated successfully!');
    }
}
