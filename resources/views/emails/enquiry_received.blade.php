<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Website Enquiry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 24px;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #162E5C;
            color: #ffffff;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }
        .header p {
            margin: 4px 0 0;
            font-size: 13px;
            color: #00A3E0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 24px;
        }
        .field-group {
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .field-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .message-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            font-size: 13px;
            line-height: 1.5;
            color: #334155;
            white-space: pre-wrap;
        }
        .footer {
            background-color: #f8fafc;
            padding: 16px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .btn-call {
            display: inline-block;
            background-color: #00A3E0;
            color: #ffffff !important;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Comfort Aircon &bull; New Customer Lead</h1>
            <p>Carrier Dealership Website Lead Alert</p>
        </div>

        <div class="content">
            <div class="field-group">
                <div class="label">Customer Full Name</div>
                <div class="value">{{ $enquiry->name }}</div>
            </div>

            <div class="field-group">
                <div class="label">Contact Mobile Number</div>
                <div class="value">
                    <a href="tel:{{ $enquiry->mobile_number }}" style="color: #162E5C; text-decoration: none;">
                        {{ $enquiry->mobile_number }}
                    </a>
                </div>
            </div>

            @if($enquiry->email)
            <div class="field-group">
                <div class="label">Email Address</div>
                <div class="value">
                    <a href="mailto:{{ $enquiry->email }}" style="color: #00A3E0; text-decoration: none;">
                        {{ $enquiry->email }}
                    </a>
                </div>
            </div>
            @endif

            <div class="field-group">
                <div class="label">Service Required</div>
                <div class="value" style="color: #162E5C;">{{ $enquiry->service_required }}</div>
            </div>

            @if($enquiry->product_interested_in)
            <div class="field-group">
                <div class="label">Product Interested In</div>
                <div class="value">{{ $enquiry->product_interested_in }}</div>
            </div>
            @endif

            <div class="field-group">
                <div class="label">Service Location</div>
                <div class="value">{{ $enquiry->location }}</div>
            </div>

            @if($enquiry->message)
            <div class="field-group">
                <div class="label">Customer Message / Requirements</div>
                <div class="message-box">{{ $enquiry->message }}</div>
            </div>
            @endif

            <div style="text-align: center; margin-top: 20px;">
                <a href="tel:{{ $enquiry->mobile_number }}" class="btn-call">
                    Call Customer ({{ $enquiry->mobile_number }})
                </a>
            </div>
        </div>

        <div class="footer">
            Lead received on {{ $enquiry->created_at->format('d M Y, h:i A') }} (Asia/Kolkata).<br>
            Comfort Aircon Admin Panel &bull; Carrier Authorized Dealer, Pondicherry
        </div>
    </div>
</body>
</html>
