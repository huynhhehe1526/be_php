<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Exception;
use MomoAPI;
use App\Services\NewPaymentService;
use App\Models\Seating;
use Hamcrest\Core\Set;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Mail;
use Laravel\Ui\Presets\React;

class BookingController extends Controller
{

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
    public function momo_payment(Request $request)
    {
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";


        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua ATM MoMo";
        //$amount = "10000";
        $amount = $_POST['total-money'];
        $orderId = time() . "";
        $redirectUrl = "http://localhost:8000";
        $ipnUrl = "http://localhost:8000";
        $extraData = "";

        $requestId = time() . "";
        $requestType = "payWithATM";

        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);
        //dd($signature);
        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = $this->execPostRequest($endpoint, json_encode($data));
        //dd($result);
        $jsonResult = json_decode($result, true);  // decode json
        return redirect()->to($jsonResult['payUrl']);
        //head('Location: ', $jsonResult['payUrl']);
        //return 'Thanh toán online nè';
    }

    public function paymentBooking(Request $req)
    {
        if (!$req->userId || !$req->showtimeId || !$req->seatId || !$req->image_payment) {
            return [
                'error' => 1,
                'message' => 'missing information'
            ];
        }

        // Tạo mới đối tượng Booking
        $booking = new Booking;
        $booking->userId = $req->userId;
        $booking->showtimeId = $req->showtimeId;
        $booking->seatId = $req->seatId;
        // echo '' . $booking->seatId;
        $seat = Seating::where('id',  $booking->seatId)
            ->select('id', 'price', 'statusSeat')
            ->first();
        if ($seat) {
            $booking->totalPrice = $seat->price;
            // $seat->statusSeat = 'SC2';
        } else {
            return [
                'error' => 2,
                'message' => 'Information not found'
            ];
        }

        // $booking->image_payment = $req->file('image_payment')->store('image11');
        $booking->image_payment = $req->file('image_payment')->store('image11');

        //lưu ở store image112
        // $image = $req->file('image_payment');
        // $imagePath = $image->store('image112', 'public');

        // $booking->image_payment = $imagePath;
        // $booking->save();
        $booking->statusBook = 'SB1';
        $existingShowtime = Booking::where('userId', $booking->userId)
            ->where('showtimeId', $booking->showtimeId)
            ->where('seatId', $booking->seatId)
            ->first();


        if ($existingShowtime) {
            return response()->json([
                'error' => 1,
                'message' => 'The information is exist in the database.'
            ]);
        }
        //$seat->save();
        $booking->save();

        if ($booking) {
            return [
                'error' => 0,
                'message' => 'Booking ticket successful!!!!',
                'data' => $booking
            ];
        }

        return [
            'error' => 3,
            'message' => 'Error booking',
        ];
    }


    public function getAllInfoUserBooking()
    {

        $seat = Booking::where('statusBook', 'SB1')
            ->with([
                'booking_user' => function ($query) {
                    $query->select('id', 'email', 'fullName');
                },
                'booking_seat' => function ($query) {
                    $query->select('id', 'price');
                },
                'showtime_booking' => function ($query) {
                    $query->select('id', 'movie_id', 'date', 'time');
                    $query->with([
                        'movie' => function ($query) {
                            $query->select('id', 'title');
                        }
                    ]);
                },
                'booking_allcode' => function ($query) {
                    $query->select('keyMap', 'valueVi', 'valueEn');
                },
            ])->get();
        return [
            'error' => 0,
            'data' => $seat
        ];
    }
    public function getConfirmBooking(Request $req)
    {
        if (!$req->id) {
            return [
                'error' => 1,
                'message' => 'Missing required params'
            ];
        }
        $booking = Booking::where('id', $req->id)
            ->with([
                'booking_user' => function ($query) {
                    $query->select('id', 'email', 'fullName');
                },
                'booking_seat' => function ($query) {
                    $query->select('id', 'price', 'chairId', 'statusSeat');
                    $query->with([
                        'chair_allcodes' => function ($query) {
                            $query->select('keyMap', 'valueVi');
                        }
                    ]);
                },
                'showtime_booking' => function ($query) {
                    $query->select('id', 'movie_id', 'location_id', 'date', 'time');
                    $query->with([
                        'location' => function ($query) {
                            $query->select('id', 'valueVi');
                        }
                    ]);
                    $query->with([
                        'movie' => function ($query) {
                            $query->select('id', 'title', 'genreId', 'actor', 'director');
                            $query->with([
                                'associate_genre' => function ($query) {
                                    $query->select('id', 'nameGenre');
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->first();

        // return [
        //     'data' => $booking
        // ];

        // echo ''.$booking->booking_user->email;
        if ($booking) {
            //echo '' . $booking->booking_user->email;
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
            $mail->addAddress($booking->booking_user->email, 'Customer');
            $mail->Subject = 'XAC NHAN DAT VE XEM PHIM';
            $mail->msgHTML('<h1>dsjadjasd</h1>
                <a>djaodaoj</a>
                <a></a>');
            $mail->Body = '<h1 style="color: red; text-align: center;">Đặt vé' . $booking->showtime_booking->movie->title . '&nbsp;thành công</h1>
            <p>Họ tên: ' . $booking->booking_user->fullName . '</p>
            <p style="font-weight: bold;">Thông tin chi tiết vé đặt</p>
            <p style="margin-left: 60px;">Tên phim: ' . $booking->showtime_booking->movie->title . '</p>
            <p style="margin-left: 60px;">Thể loại: ' . $booking->showtime_booking->movie->associate_genre->nameGenre . '</p>
            <p style="margin-left: 60px;">Đạo diễn: ' . $booking->showtime_booking->movie->director . '</p>
            <p style="margin-left: 60px;">Số ghế: ' . $booking->booking_seat->id . '</p>
            <p style="margin-left: 60px;">Loại ghế: ' . $booking->booking_seat->chair_allcodes->valueVi . '</p>
            <p style="margin-left: 60px;">Ngày chiếu: ' . $booking->showtime_booking->date . '</p>
            <p style="margin-left: 60px;">Thời gian: ' . $booking->showtime_booking->time . '</p>
            <p style="margin-left: 60px;">Địa điểm: ' . $booking->showtime_booking->location->valueVi . '</p>
            <p style="margin-left: 60px;">Tổng tiền thanh toán: ' . $booking->totalPrice . '</p>
            <p style="margin-left: 60px;">Ngày thanh toán: ' . $booking->created_at . '</p>

                <span style="color: red, font-weight: bold;">Hãy check lại kỹ thông tin vé phim nhé !!! </span>
                ';
            $booking->statusBook = 'SB2';
            $booking->booking_seat()->update(['statusSeat' => 'SC2']);
            $booking->save();
            $mail->send();
            return [
                'error' => 0,
                'data' => $booking
            ];
        }
    }

    public function getDetailBookingById(Request $req)
    {
        if (!$req->id) {
            return [
                'error' => 1,
                'message' => 'Missing required params'
            ];
        }
        $booking = Booking::where('id', $req->id)
            ->with([
                'booking_user' => function ($query) {
                    $query->select('id', 'email', 'fullName', 'phoneNumber', 'address');
                },
                'booking_seat' => function ($query) {
                    $query->select('id', 'price', 'chairId', 'statusSeat');
                    $query->with([
                        'statusSeat_allcodes' => function ($query) {
                            $query->select('keyMap', 'valueVi');
                        }
                    ]);
                    $query->with([
                        'chair_allcodes' => function ($query) {
                            $query->select('keyMap', 'valueVi');
                        }
                    ]);
                },
                'showtime_booking' => function ($query) {
                    $query->select('id', 'movie_id', 'location_id', 'date', 'time');
                    $query->with([
                        'location' => function ($query) {
                            $query->select('id', 'valueVi');
                        }
                    ]);
                    $query->with([
                        'movie' => function ($query) {
                            $query->select('id', 'title', 'genreId', 'actor', 'director');
                            $query->with([
                                'associate_genre' => function ($query) {
                                    $query->select('id', 'nameGenre');
                                }
                            ]);
                        }
                    ]);
                }
            ])
            ->first();

        return [
            'eror' => 0,
            'data' => $booking
        ];
    }

    public function getAllInfoUserConfirm()
    {

        $seat = Booking::where('statusBook', 'SB2')
            ->with([
                'booking_user' => function ($query) {
                    $query->select('id', 'email', 'fullName');
                },
                'booking_seat' => function ($query) {
                    $query->select('id', 'price');
                },
                'showtime_booking' => function ($query) {
                    $query->select('id', 'movie_id', 'date', 'time');
                    $query->with([
                        'movie' => function ($query) {
                            $query->select('id', 'title');
                        }
                    ]);
                },
                'booking_allcode' => function ($query) {
                    $query->select('keyMap', 'valueVi', 'valueEn');
                },
            ])->get();
        return [
            'error' => 0,
            'data' => $seat
        ];
    }

    public function getInforBookingUser(Request $req)
    {

        if (!$req->userId) {
            return [
                'error' => 1,
                'message' => 'Missing params required'
            ];
        } else {
            $seat = Booking::where('userId', $req->userId)
                ->with([
                    'booking_user' => function ($query) {
                        $query->select('id', 'email', 'fullName', 'phoneNumber', 'address');
                    },
                    'booking_seat' => function ($query) {
                        $query->select('id', 'price', 'chairId', 'statusSeat');
                        $query->with([
                            'statusSeat_allcodes' => function ($query) {
                                $query->select('keyMap', 'valueVi');
                            }
                        ]);
                        $query->with([
                            'chair_allcodes' => function ($query) {
                                $query->select('keyMap', 'valueVi');
                            }
                        ]);
                    },
                    'showtime_booking' => function ($query) {
                        $query->select('id', 'movie_id', 'location_id', 'date', 'time');
                        $query->with([
                            'location' => function ($query) {
                                $query->select('id', 'valueVi');
                            }
                        ]);
                        $query->with([
                            'movie' => function ($query) {
                                $query->select('id', 'title', 'genreId', 'actor', 'director');
                                $query->with([
                                    'associate_genre' => function ($query) {
                                        $query->select('id', 'nameGenre');
                                    }
                                ]);
                            }
                        ]);
                    }
                ])->get();
            return [
                'error' => 0,
                'data' => $seat
            ];
        }
    }
}
