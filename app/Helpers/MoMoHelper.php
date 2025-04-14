amespace App\Helpers;
class MoMoHelper
{
public static function generateSignature($partnerCode, $accessKey, $requestId, $amount, $orderId, $orderInfo,
$returnUrl, $notifyUrl, $secretKey)
{
$rawHash =
"partnerCode=$partnerCode&accessKey=$accessKey&requestId=$requestId&amount=$amount&orderId=$orderId&orderInfo=$orderInfo&returnUrl=$returnUrl&notifyUrl=$notifyUrl&extraData=";
return hash_hmac("sha256", $rawHash, $secretKey);
}
}