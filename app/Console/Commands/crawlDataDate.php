<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Products;
use App\Models\DataProductCrawlDate;
use App\Models\ProductPricePolicies;

class CrawlDataDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawldataDate:crawl {page?} {chunkSize=1000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        function removeTitleCrawl($string,$keyReplace = "/")
        {
            $string =   removeAccent($string);
            $string     =  trim(preg_replace("/[^A-Za-z0-9. ]/i","",$string)); // khong dau
            $string     =  str_replace(" ","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace("--","-",$string);
            $string =   str_replace($keyReplace,"-",$string);
            return strtolower($string);
        }
        function removeAccent($mystring)
        {
            $marTViet = array(
                // Chữ thường
                "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ",
                "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ",
                "ì", "í", "ị", "ỉ", "ĩ",
                "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ",
                "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
                "ỳ", "ý", "ỵ", "ỷ", "ỹ",
                "đ", "Đ", "'",
                // Chữ hoa
                "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
                "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
                "Ì", "Í", "Ị", "Ỉ", "Ĩ",
                "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
                "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
                "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
                "Đ", "Đ", "'",
            );
            $marKoDau = array(
                /// Chữ thường
                "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a",
                "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
                "i", "i", "i", "i", "i",
                "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o",
                "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
                "y", "y", "y", "y", "y",
                "d", "D", "",
                //Chữ hoa
                "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",
                "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
                "I", "I", "I", "I", "I",
                "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O",
                "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
                "Y", "Y", "Y", "Y", "Y",
                "D", "D", "",
            );

            return str_replace($marTViet, $marKoDau, $mystring);
        }
        function kiem_tra_date_con_lai($dateCrawl,$month = 6){
            $arrDateCrawl = explode('/',$dateCrawl);
            if(sizeof($arrDateCrawl) < 2){
                return false;
            }
            $year1 = $arrDateCrawl[1];
            $month1 = $arrDateCrawl[0]; // Tháng 02/2025

            // Lấy tháng và năm hiện tại
            $year2 = date('Y');
            $month2 = date('m');

            // Chuyển đổi thành tổng số tháng
            $totalMonths1 = $year1 * 12 + $month1;
            $totalMonths2 = $year2 * 12 + $month2;

            // Tính khoảng cách giữa hai mốc thời gian
            $monthDifference = abs($totalMonths1 - $totalMonths2);
            if ($monthDifference > $month) {
                return true;
            }
            return false;
        }

        // Lấy tham số từ command
        $page = $this->argument('page') ?: 1; // Trang hiện tại, mặc định là 1
        $chunkSize = $this->argument('chunkSize'); // Kích thước chunk

        $offset = ($page - 1) * $chunkSize; // Tính vị trí bắt đầu của trang
        //die;

        $query = Products::with("productpricepolicies")
        //->where('pro_id',1662)
        //->with("productpricepolicies")
        ->orderBy('pro_id')
        ->skip($offset)
        ->take($chunkSize)->get();;

        $query->chunk(50)->each(function ($datas) {
            foreach ($datas as $product) {
                $pricepolicies = 0;
                if($product->productpricepolicies){
                    foreach($product->productpricepolicies as $productpricepolicies){
                        $pricepolicies = $productpricepolicies->ppp_price;
                    }
                }

                echo $product->pro_id.' \n';
                if($product->pro_name_vn){
                    $arrData['limit'] = 1;
                    $arrData['searchTerm'] = $product->pro_name_vn;
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.giathuoctot.com/search-products-member',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_SSL_VERIFYPEER=>false,
                        CURLOPT_SSL_VERIFYHOST=>false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => json_encode($arrData),
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization:Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJVc2VyTmFtZSI6IjA5ODQxMjI3MTIiLCJQZXJtaXNzaW9ucyI6IjEwMDIsMTAwNCwyMDA0LDMwMDQsNTAwNCw2MDA0IiwiU3RhdHVzIjoiVGllcjEiLCJ1bmlxdWVfbmFtZSI6IjA5ODQxMjI3MTIiLCJIYXNBZG1pbkFjY2VzcyI6IkZhbHNlIiwibmJmIjoxNzQzNDc1ODQ5LCJleHAiOjE3NDM1NjU4NDksImlhdCI6MTc0MzQ3NTg0OX0.uTHZMm8ERJI09CWbTetw4e-Na9es_QXs8ZEgrPc6HP0'
                        ),
                    ));

                    $response = curl_exec($curl);
                    //var_dump(curl_error($curl));die;
                    curl_close($curl);
                    $dataProductCrawl = json_decode($response,true);
                    $v_flag_true = false;
                    if($product->pro_date != '' && (!$dataProductCrawl['products'][0]['additionalInfo'] || !isset($dataProductCrawl['products'][0]['additionalInfo'][4]))){
                        $v_flag_true = true;
                    }
                    if(($dataProductCrawl && intval($dataProductCrawl['total']) > 0 && $dataProductCrawl['products'][0]['additionalInfo'] && sizeof($dataProductCrawl['products'][0]['additionalInfo']) > 0 && isset($dataProductCrawl['products'][0]['additionalInfo'][4])) || $v_flag_true){
                        
                        $productCrawl = $dataProductCrawl['products'][0];
                        if($v_flag_true){
                            $valueDate = "";
                        }else{
                            $valueDate = $productCrawl['additionalInfo'][4]['value'];
                            $arrValueDate = explode(' ',$valueDate);
                            if(!kiem_tra_date_con_lai($arrValueDate[1])){
                                continue;
                            }
                        }
                        

                        $pro_name = str_replace(array('-','.'),'',removeTitleCrawl($product->pro_name_vn));
                        $productCrawl['slug'] = str_replace(array('-','.'),'',removeTitleCrawl($productCrawl['name']));
                        if($productCrawl['slug'] != $pro_name.$productCrawl['sku'] && $productCrawl['slug'] != $pro_name){
                            continue;
                        }
                        $dataProductCrawl = DataProductCrawlDate::updateOrCreate(
                            ['id' => $product->pro_id],
                            [
                                'name' => $product->pro_name_vn,
                                'date' =>$valueDate,
                            ]
                        );
                    }
                }
            }
        });
        echo "Done";
        die;
    }
}