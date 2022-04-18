<?php
namespace App\Http\Controllers;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SensorsController extends Controller
{
    public function index(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('sensors', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

        $query = DB::table('sensors');

        if ($request->project) {
            $query->where('sensors.project', $request->project);
        } else {
            if (Pariette::who('admin') == 0) {
                $query->where('sensors.project', $auth->project);
            }
        }
        if ($request->DevEUI) {
            $query->where('sensors.DevEUI', $request->DevEUI);
        }
        if ($request->type) {
            $query->where('sensors.type', $request->type);
        }
        if ($request->sf) {
            $query->where('sensors.sf', $request->sf);
        }
        if ($request->title) {
            $query->where('sensors.title', 'like', '%'.$request->title.'%');
        }
        if ($request->description) {
            $query->where('sensors.description', 'like', '%'.$request->description.'%');
        }
        if ($request->sensor_no) {
            $query->where('sensors.sensor_no', $request->sensor_no);
        }
        if ($request->created_at) {
            $query->where('sensors.created_at', $request->created_at);
        }
        if ($request->last_data_at) {
            $query->where('sensors.last_data_at', $request->last_data_at);
        }
        if (isset($request->status)) {
            $query->where('sensors.status', $request->status);
        } else {
            $query->whereNotIn('sensors.status', [0]);
        }

        $query->join('projects','projects.id','=','sensors.project');
        $query->join('companies', 'companies.id', '=', 'projects.company');
        $query->select('sensors.*', 'projects.title as projectName');

        $data = $query->get();

        if ($data) {
            return Hermes::send($data, 200);
        }
        
        return Hermes::send('lng_0001', 404);
    }

    public function store(Request $request, $storeToken)
    {
        $auth = Pariette::authRole('sensors', 'create', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }
    
        // $validator = Validator::make($request->all(), [
        //     'project' => 'required',
        //     'DevEUI' => 'required',
        //     'type' => 'required',
        //     'title' => 'required',
        //     'description' => 'required',
        //     'status' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return Hermes::send($validator->messages(), 403);
        // }

        $data = [
            'project' => $request->project,
            'DevEUI' => $request->DevEUI,
            'type' => $request->type,
            'title' => $request->title,
            'sf' => $request->sf,
            'description' => $request->description,
            'status' => $request->status ? $request->status : 1,
            'sensor_no' => $request->sensor_no,
            'created_at' => Pariette::now()
        ];

        $work = DB::table('sensors')->insertGetId($data);
        if ($work) {
            Pariette::logger('sensors:created', 'sensors.id:' . $work);
            return Hermes::send($work, 201);
        }
        return Hermes::send('lng_0003', 204);
    }

    public function show($storeToken, $id)
    {
        $auth = Pariette::authRole('sensors', 'read', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

        $data = DB::table('sensors')
            ->where('sensors.id', $id)
            ->join('projects','projects.id','=','sensors.project')
            ->select('sensors.*', 'projects.title as projectName')
            ->first();
        
        return Hermes::send($data, 200);
    }


    public function update(Request $request, $storeToken, $id)
    {
        $auth = Pariette::authRole('sensors', 'update', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }


        if ($request->store) {
            $store = $request->store;
        } else {
            $store = Pariette::token($storeToken);
        }

		$validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'status' => 'required'
        ]);
		if ($validator->fails()) {
            return Hermes::send($validator->messages(), 403);
		}
    
        $data = [];

        if (isset($request->sensor_no)) {
            $data['sensor_no'] = $request->sensor_no;
        }
        if (isset($request->project)) {
            $data['project'] = $request->project;
        }
        if (isset($request->DevEUI)) {
            $data['DevEUI'] = strval($request->DevEUI);
        }
        if (isset($request->type)) {
            $data['type'] = $request->type;
        }
        if (isset($request->title)) {
            $data['title'] = $request->title;
        }
        if (isset($request->sf)) {
            $data['sf'] = $request->sf;
        }
        if (isset($request->description)) {
            $data['description'] = $request->description;
        }
        if (isset($request->status)) {
            $data['status'] = $request->status;
        }
        
        $data['updated_at'] = Pariette::now();

        $update = DB::table('sensors')->where('id', $id)->update($data);
        
        if ($update) {
            Pariette::logger('sensors:updated', 'sensors.id:' . $id);
            return Hermes::send($data, 200);
        }
        return Hermes::send('lng_0004', 204);
    }
    

    public function destroy($id)
    {
        $auth = Pariette::authRole('sensors', 'delete', $storeToken);
        if ($auth == false) {
            return Hermes::send('lng_0002', 403);
        }

    }
}
