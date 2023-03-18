# Use Latitude and Longitude to get Zip Code for free (Taiwan)

## Installation

```bash
composer require tyeydy/map
```

## General Resources
```bash
php artsian tyeydy-map:general
```

## Usage
```php
$service = new ZipService();
$res = $service->getZipFromLatLon(25.033, 121.5651);
```
```json
{
    "name": "臺北市",
    "code": "63000",
    "towns": {
      "A17": {
        "name": "信義區",
        "code": "63000020",
        "eng": "Xinyi District"
      }
    }
  }
```
