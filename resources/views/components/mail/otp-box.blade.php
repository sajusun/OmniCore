@props([
    'code'          => '',
    'expiryMinutes' => null,
    'label'         => 'Your One-Time Password',
])
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
       style="margin-bottom: 24px;">
    <tr>
        <td align="center"
            style="background-color: #f8f9fa;
                   border: 2px dashed #dee2e6;
                   border-radius: 8px;
                   padding: 28px 20px;
                   text-align: center;">

            {{-- Label --}}
            <p style="margin: 0 0 10px;
                      color: #6c757d;
                      font-family: Arial, Helvetica, sans-serif;
                      font-size: 11px;
                      font-weight: 700;
                      letter-spacing: 2px;
                      text-transform: uppercase;
                      line-height: 1.4;
                      mso-line-height-rule: exactly;">
                {{ $label }}
            </p>

            {{-- OTP Code --}}
            <p class="otp-code"
               style="margin: 0;
                      color: #212529;
                      font-family: 'Courier New', Courier, monospace;
                      font-size: 42px;
                      font-weight: 800;
                      letter-spacing: 10px;
                      line-height: 1.2;
                      mso-line-height-rule: exactly;">
                {{ $code }}
            </p>

            @if ($expiryMinutes !== null)
            {{-- Expiry notice --}}
            <p style="margin: 12px 0 0;
                      color: #6c757d;
                      font-family: Arial, Helvetica, sans-serif;
                      font-size: 13px;
                      line-height: 1.5;
                      mso-line-height-rule: exactly;">
                &#x23F1; Expires in
                <strong>{{ $expiryMinutes }} minute{{ $expiryMinutes != 1 ? 's' : '' }}</strong>
            </p>
            @endif

        </td>
    </tr>
</table>
