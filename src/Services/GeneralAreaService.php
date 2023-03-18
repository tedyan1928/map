<?php

namespace Tyeydy\Map\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GeneralAreaService
{
    public function generalArea()
    {
        /* 產生資料方法
         * 我國各鄉(鎮、市、區)行政區域界線圖資
         * https://data.gov.tw/dataset/7441
         * .dbf, .prj, .shp, .shx 檔案一同轉出 geojson
         * https://products.aspose.app/gis/conversion/shapefile-to-json
         *
         * */
        ini_set('memory_limit','1024M');

        $file = File::get(__DIR__ . '/../Resources/TOWN_MOI_1100415.geojson');
        $datas = json_decode($file, true);
        $infos = [];
        foreach ($datas['features'] as $index => $data) {
            $infos[] = [
                'property' => $data['properties'],
                'maxRange' => $this->getMaxRange($data['geometry']['coordinates']),
            ];
            $path = 'area/' . $data['properties']['COUNTYCODE'] . '/' . $data['properties']['TOWNCODE']. '.json';

            if (!is_float($data['geometry']['coordinates'][0][0][0])) {
                foreach ($data['geometry']['coordinates'] as $index2 => $area) {
                    $data['geometry']['coordinates'][$index2] = $data['geometry']['coordinates'][$index2][0];
                }
            }

            Storage::disk('public')->put($path, json_encode($data['geometry']['coordinates']));
        };

        Storage::disk('public')->put('area/max_range.json', json_encode($infos));
    }

    // 一個區域有多個圈所組成
    private function getMaxRange(array $multiAreas)
    {
        // [區域index][index][latlng]
        if (is_float($multiAreas[0][0][0])) {
            $maxX = $multiAreas[0][0][0];
            $minX = $multiAreas[0][0][0];
            $maxY = $multiAreas[0][0][1];
            $minY = $multiAreas[0][0][1];
        } else {
            $maxX = $multiAreas[0][0][0][0];
            $minX = $multiAreas[0][0][0][0];
            $maxY = $multiAreas[0][0][0][1];
            $minY = $multiAreas[0][0][0][1];
        }

        foreach ($multiAreas as $latLngs) {
            if (!is_float($latLngs[0][0])) {
                $latLngs = $latLngs[0];
            }
            foreach ($latLngs as $latLng) {
                if ($latLng[0] > $maxX) {
                    $maxX = $latLng[0];
                }
                if ($latLng[0] < $minX) {
                    $minX = $latLng[0];
                }
                if ($latLng[1] > $maxY) {
                    $maxY = $latLng[1];
                }
                if ($latLng[1] < $minY) {
                    $minY = $latLng[1];
                }
            }
        }

        return [
            'maxX' => $maxX,
            'minX' => $minX,
            'maxY' => $maxY,
            'minY' => $minY,
        ];
    }
}
