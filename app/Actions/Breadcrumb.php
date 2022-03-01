<?php

namespace App\Actions;

use App\Helpers\Hermes;
use App\Helpers\Pariette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if($request->companies == true) {
            $crumb['active'] = 'Companies';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard']
            ];
        }
        else if($request->projects == true) {
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
        }
        else if($request->sensors == true) {
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
        }
        else if($request->measurement == true) {
            $crumb['active'] = 'Measurement';
            if ($request->sensor) {
                $row = DB::table('sensors')
                ->where('sensors.id', $request->sensor)
                ->join('projects', 'projects.id', '=', 'sensors.project')
                ->join('companies', 'companies.id', '=', 'projects.company')
                ->select('sensors.title as sensorName', 'projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                ->first();
                $crumb['items'] = [
                    ['title' => $row->companyName, 'route' => 'Companies', 'locale' => true],
                    ['title' => $row->projectName, 'route' => 'Projects?company='. $row->companyId, 'locale' => true],
                    ['title' => $row->sensorName, 'route' => 'Sensors?project='. $row->projectId, 'locale' => true],
                ];
            }
            else if ($request->project) {
                $row = DB::table('projects')
                ->where('projects.id', $request->project)
                ->join('companies', 'companies.id', '=', 'projects.company')
                ->select('projects.title as projectName', 'projects.id as projectId', 'companies.title as companyName', 'companies.id as companyId')
                ->first();
                $crumb['items'] = [
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

        }
        else if($request->uplink == true) {
            $crumb['active'] = 'Uplink';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ['title' => 'Companies', 'route' => 'Companies'],
                ['title' => 'Projects', 'route' => 'Projects'],
            ];
        }
        else if($request->mixCalibration == true) {
            $crumb['active'] = 'MixCalibration';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ['title' => 'Companies', 'route' => 'Companies'],
                ['title' => 'Projects', 'route' => 'Projects'],
            ];
        }
        else if($request->mix == true) {
            $crumb['active'] = 'Mix';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ['title' => 'Companies', 'route' => 'Companies'],
                ['title' => 'Projects', 'route' => 'Projects'],
            ];
        }
        else if($request->log == true) {
            $crumb['active'] = 'Logs';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
            ];     
        }
        else if($request->authority == true) {
            $crumb['active'] = 'Authority';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
                ['title' => 'Companies', 'route' => 'Companies'],
                ['title' => 'Projects', 'route' => 'Projects'],
            ];      
        }
        else if($request->downlink == true) {
            $crumb['active'] = 'Downlink';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
            ];          
        }
        else if($request->users == true) {
            $crumb['active'] = 'Users';
            $crumb['items'] = [
                ['title' => 'AkilliBeton', 'route' => 'Dashboard'],
            ];          
        }

        return Hermes::send($crumb, 200);
    }
}
