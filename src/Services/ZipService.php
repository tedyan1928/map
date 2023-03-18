<?php

namespace Tyeydy\Map\Services;

use Illuminate\Support\Facades\Storage;

class ZipService
{
    public function getZipFromLatLon($lat, $lng)
    {
        $point = [$lng, $lat];

        $maxRangeAreas = json_decode(Storage::disk('public')->get('area/max_range.json'), true);
        $qualifierAreas = [];
        foreach ($maxRangeAreas as $area) {
            if ($area['maxRange']['maxX'] >= $point[0] && $area['maxRange']['minX'] <= $point[0] &&
                $area['maxRange']['maxY'] >= $point[1] && $area['maxRange']['minY'] <= $point[1]) {
                $qualifierAreas[] = $area;
            }
        }

        if (empty($qualifierAreas)) {
            return false;
        }

        foreach ($qualifierAreas as $area) {
            $filePath = 'area/' . $area['property']['COUNTYCODE'] . '/' . $area['property']['TOWNCODE'] . '.json';
            $blocks = json_decode(Storage::disk('public')->get($filePath), true);
            foreach ($blocks as $block) {
                $startPoint = $block[0];
                $count = 0;
                $inside = true;
                for ($i = 1 ; $i < count($block) ; $i++) {
                    $endPoint = $block[$i];
                    if (($point[0] == $startPoint[0] && $point[1] == $startPoint[1])
                        || ($point[0] == $endPoint[0] && $point[1] == $endPoint[1])) {
                        $inside = false;
                    }

                    if ($point[1] == min($startPoint[1], $endPoint[1])) {
                        $startPoint = $endPoint;
                        continue;
                    }

                    $maxY = max($startPoint[1], $endPoint[1]);
                    $minY = min($startPoint[1], $endPoint[1]);
                    if ($point[1] <= $minY || $point[1] >= $maxY) {
                        $startPoint = $endPoint;
                        continue;
                    }

                    $x = $endPoint[0] - ($endPoint[1] - $point[1]) * ($endPoint[0] - $startPoint[0]) / ($endPoint[1] - $startPoint[1]);


                    if ($x < $point[0]) {
                        $count += 1;
                    } else if ($x == $point[0]) {
                        $inside = false;
                    }

                    $startPoint = $endPoint;
                }

                if ($count % 2 == 0 || !$inside) {
                    continue;
                }

                return $area['property'];
            }
        }
    }
}
