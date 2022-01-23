<?php

namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UplinkController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        // if (!$this->controlUser($request->store, 'uplink', 'read')) {
        //     return Hermes::send('lng_0002', 401);
        // }
        $query = DB::table('uplink');

        if ($request->search) {
            $query->where('DevEUI', $request->search);
        }

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $validator = Validator::make($request->all(), [
            'DevEUI_uplink' => 'required'
        ]);

        if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
        }

        function hex_to_string($hex) {
            if (strlen($hex) % 2 != 0) {
                throw new Exception('String length must be an even number.', 1);
            }
            $string = '';
            for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
                $string .= chr(hexdec($hex[$i].$hex[$i+1]));
            }
            return $string;
        }

        $datas = $request->json()->get('DevEUI_uplink');;
        $DevEUI = $datas['DevEUI'];
        $device = DB::table('sensors')->where('DevEUI',$DevEUI)->first();

        if ($device) {
            $uplinkHex = $datas['payload_hex'];
            $splitQuery = explode(' ', hex_to_string($uplinkHex));
            $temp = explode('=',$splitQuery[0]);
            $deviceTemp = $temp[1]; // burası çok önemli!!
            $createdAt = Pariette::now();

            $work = DB::table('uplink')->insert([
                'DevEUI' => $datas['DevEUI'],
                'payload_hex' => $datas['payload_hex'],
                'LrrRSSI' => $datas['LrrRSSI'],
                'LrrSNR' => $datas['LrrSNR'],
                'temperature' => $deviceTemp,
                'maturity' => rand(10,100),
                'created_at' => $createdAt
            ]);

            if ($work) {
                $devUpdt = array();
                if($device->readed_max >= $deviceTemp) {
                    $devUpdt['readed_max'] = $deviceTemp;
                }
                if($device->readed_min <= $deviceTemp) {
                    $devUpdt['readed_min'] = $deviceTemp;
                }
                $devUpdt['last_data_at'] = $createdAt;

                DB::table('measurement')->where('id',$DevEUI)->update($devUpdt);

                return Hermes::send($work, 201);
            } else {
                return Hermes::send('lng_0003', 204);
            }
        }
    }

    public function show(Request $request, $storeToken, $id)
    {
        $query = DB::table('uplink')
            ->where('uplink.DevEUI', $id)
            ->offset(0)
            ->limit($request->limit)
            ->orderBy('id', 'DESC');
        if ($request->measurement) {
            $query->where('measurement', $request->measurement);
        }
        $uplinkdata = $query->get();
        $sensor = DB::table('sensors')->where('DevEUI', $id)->first();
        $project = DB::table('projects')->where('id', $sensor->project)->first();
        $data = [
            'uplinkdata' => $uplinkdata,
            'sensor' => $sensor,
            'project' => $project
        ];
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        if (!$this->controlUser('uplink', 'update')) {
            return Hermes::send('lng_0002', 401);
        }
		$validator = Validator::make($request->all(), [
            'measurement' => 'required',
            'DevEUI' => 'required',
            'payload_hex' => 'required',
            'LrrRSSI' => 'required',
            'LrrSNR' => 'required',
            'temperature' => 'required',
            'maturity' => 'required',
            'strength' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [
            'measurement' => $request->measurement,
            'DevEUI' => $request->DevEUI,
            'payload_hex' => $request->payload_hex,
            'LrrRSSI' => $request->LrrRSSI,
            'LrrSNR' => $request->LrrSNR,
            'temperature' => $request->temperature,
            'maturity' => $request->maturity,
            'strength' => $request->strength,
            'updated_at' => Pariette::now()
        ];

        $update = DB::table('uplink')->where('id', $id)->update($data);
        
        if ($update) {
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
    }
}
