<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Illuminate\Mail\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ForgotPass;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Auth\buildUrlEmail;
use Exception;

require_once 'D:\namba\hk2\LapTrinh_PHP\doan\thu_laidb_web_ticket_movie\backend\vendor\autoload.php';

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    // function sendSimpleEmail($dataSend)
    // {
    //     $receiverEmail = $dataSend['receiverEmail'];
    //     $redirectLink = $dataSend['redirectLink'];

    //     Mail::send([], [], function ($message) use ($receiverEmail, $redirectLink) {
    //         $message->to($receiverEmail)
    //             ->subject('Xác nhận email thay đổi password')
    //             ->setBody('<p>Click vào link bên dưới để xác thực email</p><div><a href="' . $redirectLink . '" target="_blank">Verify</a></div>', 'text/html')
    //             ->from('danghuynh1526@gmail.com', 'Huỳnh nè 👻');
    //     });
    // }


    //confirm change pass
    public function verifyChangePass(Request $req)
    {
        try {
            if (!$req['token']) {
                return [
                    'errCode' => 1,
                    'errMessage' => 'Missing parameter'
                ];
            } else {
                $changePassWord = ForgotPass::where('token', $req['token'])
                    ->where('statusId', 'SP1')
                    ->first();

                if ($changePassWord) {
                    $changePassWord->statusId = 'SP2';
                    $changePassWord->save();

                    return [
                        'errCode' => 0,
                        'errMessage' => 'Reset password successful!!'
                    ];
                } else {
                    return [
                        'errCode' => 2,
                        'errMessage' => 'Error reset password'
                    ];
                }
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    // Trong Laravel Controller
    public function buildUrlEmail($token)
    {
        $result = env('URL_REACT') . '/verify_email?token=' . $token . '';
        return $result;
    }
    public function forgotPassword(Request $request)
    {
        $forgotPass = new ForgotPass;
        if (!$request->email) {
            return [
                'error' => 1,
                'message' => 'Please input email'
            ];
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return [
                'error' => 2,
                'message' => 'Email not found'
            ];
        } else {
            $token = Str::random(16);
            $redirectLink = $this->buildUrlEmail($token);

            $forgotPass->userId = $user->id;
            if ($request->new_pass !== $request->confirm_pass) {
                return [
                    'error' => 3,
                    'message' => 'New password and confirm password do not match'

                ];
            }
            $forgotPass->new_pass = Hash::make($request->new_pass);
            $forgotPass->confirm_pass = Hash::make($request->confirm_pass);
            $forgotPass->token = $token;
            $forgotPass->statusId = 'SP1';
            $forgotPass->save();


            $user->password = Hash::make($request->new_pass);
            $user->save();
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->SMTPDebug = 2;
            $mail->Host = env('MAIL_HOST');
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->setFrom(env('MAIL_USERNAME'), 'page_sell_ticket_movie');
            $mail->addReplyTo(env('MAIL_USERNAME'), 'page_sell_ticket_movie');
            $mail->addAddress($request->email, 'Receiver Name');
            $mail->Subject = 'Reset password';
            $mail->msgHTML('<h1>dsjadjasd</h1>
                <a>djaodaoj</a>
                <a></a>');
            $mail->Body = '<h1 style="color: red; text-align: center;">Xác nhận thay đổi passwordl!!!!</h1>
                <a href="' . $redirectLink . '">Click vào đây để cập nhật password</a>
                ';
            // $forgotPass->statusId = 'SP2';
            // $forgotPass->save();
            // return 
            return [
                'error' => 0,
                'message' => 'Check email!',
                $mail->send()
            ];
        }






        // // Kiểm tra xem người dùng có tồn tại không
        // $user = User::where('email', $request['email'])->first();
        // if (!$user) {
        //     return response()->json([
        //         'error' => 1,
        //         'message' => 'Người dùng không tồn tại'
        //     ], 404);
        // }

        // // Tạo hoặc cập nhật thông tin quên mật khẩu
        // $forgotPassword = ForgotPass::updateOrCreate(
        //     ['user_id' => $user->id],
        //     ['token' => Str::random(60)] // Đây chỉ là ví dụ, bạn có thể tạo mã token một cách phức tạp hơn
        // );

        // // Gửi email chứa liên kết reset mật khẩu tới email của người dùng 
        // // (Bạn có thể sử dụng Mail::to()->send() như đã thảo luận trong các bài trước)

        // return response()->json(
        //     [
        //         'error' => 0,
        //         'message' => 'Email reset mật khẩu đã được gửi'
        //     ]
        // );

    }


    // function forgotpassword()
    // {
    // $receiverEmail = $req['receiverEmail'];
    // $redirectLink = $req['redirectLink'];

    // Mail::send([], [], function ($message) {
    //     $message->to('nhuhuynhdang.191203@gmail.com')
    //         ->subject('Xác nhận email thay đổi password')
    //         ->setBody('<p>Click vào link bên dưới để xác thực email</p><div><a href="'  . '" target="_blank">Verify</a></div>', 'text/html')
    //         ->from('danghuynh1526@gmail.com', 'Huỳnh nè 👻');
    // });

    // Mail::raw('Đây là nội dung email bình thường', function ($message) {
    //     $message->to('nhuhuynhdang.191203@gmail.com', 'Người nhận')
    //         ->subject('<h1>Tiêu đề email bình thường</h1>')
    //         ->setBody('<h1>Click vào link bên dưới để xác thực email</h1>', 'text/html')
    //         ->from('danghuynh1526@gmail.com', 'Huỳnh nè 👻');
    // });


    // Mail::raw(
    //     'Đây là nội dung email bình thường',
    //     function ($message) {
    //         $message->to('nhuhuynhdang.191203@gmail.com', 'Người nhận')
    //             ->subject('Tiêu đề email bình thường')
    //             ->setBody('<h1>Click vào link bên dưới để xác thực email</h1>', 'text/html')
    //             ->from('danghuynh1526@gmail.com', 'Huỳnh nè 👻');
    //     }
    // );


    // Mail::send([], [], function ($message) {
    //     $message->to('nhuhuynhdang.191203@gmail.com')
    //         ->subject('Xác nhận email thay đổi password')
    //         ->setBody('<p>Click vào link bên dưới để xác thực email</p><div><a href="YOUR_REDIRECT_LINK" target="_blank">Verify</a></div>', 'text/html')
    //         ->from('danghuynh1526@gmail.com', 'Huỳnh nè 👻');
    // });
    // $transport = new Swift_SmtpTransport('smtp.gmail.com', 587);

    // $mailer = new Swift_Mailer($transport);

    // // Tạo đối tượng Swift_Message
    // $message = new Swift_Message('Tiêu đề email');
    // //$message->setFrom(['danghuynh1526@gmail.com' => 'Huỳnh nè 👻']);
    // $message->setFrom(['danghuynh1526@gmail.com' => 'page_sell_ticket_movie']);

    // $message->setTo(['nhuhuynhdang.191203@gmail.com' => 'Recipient Name']);
    // $message->setBody('<h1>Nội dung email</h1>', 'text/html');

    // // Gửi email
    // $mailer->send($message);

    //return 'Thay đổi pass nè';
    //     }
}
