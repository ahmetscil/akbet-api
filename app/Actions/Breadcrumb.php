<?php

/*
    query yoksa oturum açtığı proje veya company datasını alabilir.
    bu geliştirme faz2ye bırakılsın.
*/

namespace App\Actions;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class Breadcrumb
{
    use AsAction;

    public function asController(Request $request)
    {
        return $this->handle($request);
    }
  
    public function handle($request)
    {
        $crumb = [];
        $crumb['active'] = '';
        $crumb['items'] = [];

        switch ($request->where) {
            case 'companies':
                $crumb['active'] = 'Companies';
                $crumb['items'] = [
                    ['title' => 'AkilliBeton', 'route' => 'Dashboard']
                ];    
                break;
            case 'projects': 
                $crumb['active'] = 'Projects';
                if ($request->company) {
                    $row = DB::table('companies')
                    ->where('companies.id', $request->company)
                    ->first();
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->title, 'route' => 'Companies', 'locale' => true]
                    ];
                } else {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => 'Companies', 'route' => 'Companies']
                    ];
                }    
                break;
            case 'sensors':
                $crumb['active'] = 'Sensors';
                if ($request->project) {
                    $row = DB::table('projects')
                    ->where('projects.id', $request->project)
                    ->join('companies', 'companies.id', '=', 'projects.company')
                    ->select('projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                    ->first();
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                        ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                    ];
    
                } else {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => 'Companies', 'route' => 'Companies'],
                        ['title' => 'Projects', 'route' => 'Projects'],
                    ];
                }
                break;
            case 'measurement':
                $crumb['active'] = 'Measurement';

                if ($request->sensor) {
                    $row = DB::table('sensors')
                    ->where('sensors.id', $request->sensor)
                    ->join('projects', 'projects.id', '=', 'sensors.project')
                    ->join('companies', 'companies.id', '=', 'projects.company')
                    ->select('sensors.title as sensorName', 'projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                    ->first();
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                        ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                        ['title' => $row->sensorName, 'route' => 'Sensors?project='. $row->projectId, 'locale' => true],
                    ];
                } else if ($request->project) {
                    $row = DB::table('projects')
                    ->where('projects.id', $request->project)
                    ->join('companies', 'companies.id', '=', 'projects.company')
                    ->select('projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                    ->first();
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                        ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                    ];
    
                } else {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => 'Companies', 'route' => 'Companies'],
                        ['title' => 'Projects', 'route' => 'Projects'],
                    ];
                }
                break;
            case 'uplink':
                $crumb['active'] = 'Uplink';

                $row = DB::table('uplink')
                ->where('uplink.measurement', $request->uplink)
                ->join('measurement', 'measurement.id', '=', 'uplink.measurement')
                ->join('sensors', 'sensors.id', '=', 'measurement.sensor')
                ->join('projects', 'projects.id', '=', 'sensors.project')
                ->join('companies', 'companies.id', '=', 'projects.company')
                ->select(
                    'uplink.DevEUI',
                    'measurement.name as measurementName',
                    'measurement.id as measurementId',
                    'sensors.id as sensorId',
                    'sensors.title as sensorName',
                    'projects.id as projectId',
                    'projects.title as projectName',
                    'companies.id as companyId',
                    'companies.title as companyName'
                )
                ->first();
                if ($row) {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                        ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                        ['title' => $row->sensorName, 'route' => 'Sensors?project='. $row->projectId, 'locale' => true],
                        ['title' => $row->measurementName, 'route' => 'Measurement?sensor='. $row->sensorId, 'locale' => true],
                    ];
                } else {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => 'Companies', 'route' => 'Companies'],
                        ['title' => 'Projects', 'route' => 'Projects'],
                        ['title' => 'Sensors', 'route' => 'Projects'],
                        ['title' => 'Measurement', 'route' => 'Measurement'],
                    ];
                }
                break;
            case 'mixCalibration':
                $crumb['active'] = 'MixCalibration';
                if ($request->mix) {
                    $row = DB::table('mix')
                    ->where('mix.id', $request->mix)
                    ->join('projects', 'projects.id', '=', 'mix.project')
                    ->join('companies', 'companies.id', '=', 'projects.company')
                    ->select('mix.title as mixTitle', 'mix.id as mixId', 'projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                    ->first();
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                        ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                        ['title' => $row->mixTitle, 'route' => 'Mix?project='. $row->projectId, 'locale' => true],
                    ];
    
                } else {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => 'Companies', 'route' => 'Companies'],
                        ['title' => 'Projects', 'route' => 'Projects'],
                    ];
                }
                break;
            case 'mix':
                $crumb['active'] = 'Mix';

                if ($request->project) {
                    $row = DB::table('projects')
                    ->where('projects.id', $request->project)
                    ->join('companies', 'companies.id', '=', 'projects.company')
                    ->select('projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                    ->first();
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                        ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                    ];
    
                } else {
                    $crumb['items'] = [
                        ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                        ['title' => 'Companies', 'route' => 'Companies'],
                        ['title' => 'Projects', 'route' => 'Projects'],
                    ];
                }

                break;
            case 'log':
                $crumb['active'] = 'Logs';
                $crumb['items'] = [
                    ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ];     
                break;
            case 'authority':
                $crumb['active'] = 'Authority';
                $crumb['items'] = [
                    ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                    ['title' => 'Companies', 'route' => 'Companies'],
                    ['title' => 'Projects', 'route' => 'Projects'],
                ];      
                break;
            case 'downlink':
                $crumb['active'] = 'Downlink';
                $crumb['items'] = [
                    ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ];          
                break;
            case 'users':
                $crumb['active'] = 'Users';
                $crumb['items'] = [
                    ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ];
                break;
            default:
            # code...
            break;
        }
        return Hermes::send($crumb, 200);

    }
}
