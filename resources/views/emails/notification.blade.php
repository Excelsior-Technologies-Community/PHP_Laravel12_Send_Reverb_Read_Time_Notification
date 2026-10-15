<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notification</title>
</head>

<body style="margin:0;padding:30px;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">

                <table width="650" cellpadding="0" cellspacing="0" border="0"
                    style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.08);">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#2563eb,#7c3aed);padding:35px;text-align:center;color:#ffffff;">

                            <div style="font-size:52px;margin-bottom:10px;">
                                🔔
                            </div>

                            <h1 style="margin:0;font-size:30px;font-weight:bold;">
                                New Notification
                            </h1>

                            <p style="margin-top:10px;font-size:16px;color:#e5e7eb;">
                                You have received a new notification from the system.
                            </p>

                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:35px;">

                            <h2 style="margin-top:0;color:#111827;">
                                Hello,
                            </h2>

                            <p style="font-size:16px;color:#4b5563;line-height:28px;">
                                A new notification has been sent to your account.
                                Below are the complete details.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="margin-top:30px;border-collapse:collapse;">

                                <tr>
                                    <td width="170"
                                        style="padding:15px;background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;">
                                        Message
                                    </td>

                                    <td
                                        style="padding:15px;border:1px solid #e5e7eb;color:#374151;">
                                        {{ $notification->data['message'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="padding:15px;background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;">
                                        Category
                                    </td>

                                    <td
                                        style="padding:15px;border:1px solid #e5e7eb;">

                                        <span
                                            style="display:inline-block;padding:7px 16px;background:#ede9fe;color:#6d28d9;border-radius:30px;font-weight:bold;text-transform:uppercase;font-size:12px;">
                                            {{ $notification->category }}
                                        </span>

                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="padding:15px;background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;">
                                        Priority
                                    </td>

                                    <td
                                        style="padding:15px;border:1px solid #e5e7eb;">

                                        @php
                                        $bg = '#dcfce7';
                                        $color = '#15803d';

                                        if($notification->priority == 'medium'){
                                        $bg = '#fef3c7';
                                        $color = '#b45309';
                                        }

                                        if($notification->priority == 'high'){
                                        $bg = '#fee2e2';
                                        $color = '#b91c1c';
                                        }
                                        @endphp

                                        <span
                                            style="display:inline-block;padding:7px 16px;background:{{ $bg }};color:{{ $color }};border-radius:30px;font-weight:bold;text-transform:uppercase;font-size:12px;">
                                            {{ strtoupper($notification->priority) }}
                                        </span>

                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="padding:15px;background:#f9fafb;border:1px solid #e5e7eb;font-weight:bold;">
                                        Date & Time
                                    </td>

                                    <td
                                        style="padding:15px;border:1px solid #e5e7eb;color:#374151;">
                                        {{ now()->format('d M Y, h:i A') }}
                                    </td>
                                </tr>

                            </table>

                            <div style="margin-top:35px;text-align:center;">

                                @if(!empty($notification->data['action_text']) && !empty($notification->data['action_url']))
                                <a href="{{ $notification->data['action_url'] }}"
                                    style="display:inline-block;background:#7c3aed;color:#ffffff;text-decoration:none;padding:14px 34px;border-radius:8px;font-size:16px;font-weight:bold;margin-right:10px;">
                                    {{ $notification->data['action_text'] }}
                                </a>
                                @endif

                                <a href="{{ url('/') }}"
                                    style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;padding:14px 34px;border-radius:8px;font-size:16px;font-weight:bold;">
                                    Open Application
                                </a>

                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding:25px;background:#f9fafb;text-align:center;border-top:1px solid #e5e7eb;">

                            <p style="margin:0;font-size:14px;color:#6b7280;">
                                This is an automated notification from
                                <strong>{{ config('app.name') }}</strong>.
                            </p>

                            <p style="margin-top:10px;font-size:13px;color:#9ca3af;">
                                Please do not reply to this email.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>