<?php

namespace App\Helper;

class HCurl {
    static function get($fullpath, $data=[]) {
        $response = ['code' => 500, 'data' => trans('system.have_an_error')];
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullpath);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, 'api:' . env('MAILGUN_API_KEY'));
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            if (isset($info['http_code'])) $response['code'] = $info['http_code'];
            //de code
            if ($response['code'] >=200 && $response['code'] < 300) {
                $response['data']  = json_decode($output, 1);
            } elseif ($output) {
                $response['data'] = $output;//message error
            }
        } catch (\Exception $e) {
            $response['data'] = $e->getMessage();
        } finally {
            return $response;
        }
    }

    static function post($fullpath, $data=[]) {
        $response = ['code' => 500, 'data' => trans('system.have_an_error')];
        $data = json_encode($data);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fullpath);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);

            $output = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            if (isset($info['http_code'])) $response['code'] = $info['http_code'];
            //de code
            $output = json_decode($output, 1);
            if ($response['code'] >=200 && $response['code'] < 300) {
                $response['data']  = $output;
            } elseif ($output) {
                $response['data'] = $output['message'];//message error
            }
        } catch (\Exception $e) {
            $response['data'] = $e->getMessage();
        } finally {
            return $response;
        }
    }

    static function put($path, $data=[]) {
        $response = ['code' => 500, 'data' => trans('system.have_an_error')];
        $data = json_encode($data);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('MAILGUN_API_URL') . $path);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data)
            ));
            curl_setopt($ch, CURLOPT_USERPWD, 'api:' . env('MAILGUN_API_KEY'));
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $output = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            if (isset($info['http_code'])) $response['code'] = $info['http_code'];
            //de code
            $output = json_decode($output, 1);
            if ($response['code'] >=200 && $response['code'] < 300) {
                $response['data']  = $output;
            } elseif ($output) {
                $response['data'] = $output['message'];//message error
            }
        } catch (\Exception $e) {
            $response['data'] = $e->getMessage();
        } finally {
            return $response;
        }
    }
}

?>