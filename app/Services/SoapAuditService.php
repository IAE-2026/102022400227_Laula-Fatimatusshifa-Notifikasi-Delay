<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SoapAuditService
{
    public function sendAudit($token, $logContent)
    {
        $xml = '
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:iae="http://iae.central/audit">

<soap:Body>
<iae:AuditRequest>

<iae:TeamID>TEAM-12</iae:TeamID>

<iae:ActivityName>
DelayNotificationSent
</iae:ActivityName>

<iae:LogContent><![CDATA[' .
$logContent .
']]></iae:LogContent>

</iae:AuditRequest>
</soap:Body>

</soap:Envelope>';

        return Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'text/xml'
            ])
            ->send(
                'POST',
                'https://iae-sso.virtualfri.id/soap/v1/audit',
                ['body' => $xml]
            )
            ->body();
    }
}