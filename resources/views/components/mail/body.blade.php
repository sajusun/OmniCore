{{--
    ROW-LEVEL COMPONENT: renders a single <tr> with a padded <td>.
    Place content-level components inside:
      <x-bootstrap-mail.text>, <x-bootstrap-mail.button>, <x-bootstrap-mail.alert>, <x-bootstrap-mail.otp-box>
--}}
<tr>
    <td class="email-body"
        style="padding: 36px 40px 28px;
               font-family: Arial, Helvetica, sans-serif;
               font-size: 15px;
               line-height: 1.7;
               color: #374151;">
        {{ $slot }}
    </td>
</tr>
