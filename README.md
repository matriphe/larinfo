# Larinfo

[![Larinfo](https://github.com/matriphe/larinfo/actions/workflows/larinfo.yml/badge.svg)](https://github.com/matriphe/larinfo/actions/workflows/larinfo.yml)
[![Total Download](https://img.shields.io/packagist/dt/matriphe/larinfo.svg)](https://packagist.org/packages/matriphe/larinfo)
[![Latest Stable Version](https://img.shields.io/packagist/v/matriphe/larinfo.svg)](https://packagist.org/packages/matriphe/larinfo)

Larinfo provide system information for Laravel. 

It wraps [Linfo](https://github.com/jrgp/linfo) to show IP address information on the server and client side, server software versions, and hardware information.

## Requirements

- **PHP version**: `^8.0.2`.
- **Laravel version**: `^9.0`.

### For Windows User

It is recommended to [enable `com_dotnet` extension](https://www.php.net/manual/en/com.installation.php) to get more accurate information.

In your `php.ini` file, add this line, and make sure you have `php_com_dotnet.dll` in your PHP `ext` directory.
```ini
extension=com_dotnet
```

## Installation

To install using [Composer](https://getcomposer.org/), just run this command below.

```shell
composer require matriphe/larinfo
```
### Older Version

- Laravel `5.0`, `5.1`, `5.2`, `5.3`, `5.4`, `5.5`, and `5.6`, **[please use version 2.2](https://github.com/matriphe/larinfo/releases/tag/2.2)** by running `composer require matriphe/larinfo:2.2`.
- Laravel `5.7.*`, `5.8.*`, `^6.0`, `^7.0`, and `^8.0`, **[please use version 3.0.0](https://github.com/matriphe/larinfo/releases/tag/3.0.0)** by running `composer require matriphe/larinfo:3.0.0`.

### Configuration

To publish the config (optional) run this command below.

```shell
php artisan vendor:publish
```

Then select the number that points to `Matriphe\Larinfo\LarinfoServiceProvider` provider.

The new config will be placed in `config/larinfo.php`.

#### Service Configuration

IP address information is taken using [ipinfo.io](http://ipinfo.io/) service. If you've registered and has access token, put your token in the `config/services.php` inside the `ipinfo` variable.

```php
'ipinfo' => [
    'token'  => 'your_ipinfo_token',
]
```

If you don't want to hit ipinfo.io rate limit, it is recommended to cache it using Laravel built-in cache.

## Usage

To get all info, use facade `Larinfo` and call the `getInfo()` method. It will return this array example.

```php
use Larinfo;

$larinfo = Larinfo::getInfo();
```

The result of that command is shown below.

```php
$larinfo = [
   'host'=> [
       'city'=> 'San Francisco',
       'country'=> 'US',
       'hostname'=> '',
       'ip'=> '104.20.8.94',
       'loc'=> '37.7697,-122.3933',
       'org'=> 'AS13335 Cloudflare, Inc.',
       'phone'=> '',
       'postal'=> '94107',
       'region'=> 'California',
       'timezone' => 'America/Los_Angeles',
   ],
   'client'=> [
       'city'=> 'Bekasi',
       'country'=> 'ID',
       'hostname'=> '',
       'ip'=> '180.252.202.108',
       'loc'=> '-6.2349,106.9896',
       'org'=> 'AS17974 PT Telekomunikasi Indonesia',
       'phone'=> '',
       'postal'=> '',
       'region'=> ''
   ],
   'server'=> [
       'software'=> [
           'os'=> 'MacOS',
           'distro'=> 'MacOS 10.15.7',
           'kernel'=> '19.6.0',
           'arc'=> 'x86_64',
           'webserver'=> 'nginx/1.19.8',
           'php'=> '8.0.3'
       ],
       'hardware'=> [
           'cpu'=> 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
           'cpu_count'=> 4,
           'model'=> 'Apple device',
           'virtualization'=> '',
           'ram'=> [
               'total'=> 8589934592,
               'free'=> 8578883584,
               'human_total' => '8.0 GiB',
               'human_free' => '15.0 MiB',
           ],
           'swap'=> [
               'total'=> 2147483648,
               'free'=> 426246144,
               'human_total' => '2.0 GiB',
               'human_free' => '406.5 MiB',
           ],
           'disk'=> [
               'total'=> 2999590176768,
               'free'=> 1879852326912,
               'human_total' => '2.7 TiB',
               'human_free' => '1.7 TiB',
           ]
       ],
       'uptime'=> [
           'uptime'=> '2 days, 12 hours, 13 minutes, 43 seconds',
           'booted_at'=> '2021-04-02 15:27:54'
       ]
   ],
   'database'=> [
       'driver'=> 'MySQL',
       'version'=> '8.0.22'
   ]
];
```

Other method you can use are:

* `getHostIpinfo` to get host IP info (`Larinfo::getHostIpinfo()`)
* `getClientIpinfo` to get client IP info (`Larinfo::getClientIpinfo()`)
* `getServerInfoSoftware` to get server software info (`Larinfo::getServerInfoSoftware()`)
* `getServerInfoHardware` to get server hardware info (`Larinfo::getServerInfoHardware()`)
* `getUptime` to get server uptime (`Larinfo::getUptime()`)
* `getServerInfo` to get server info (`Larinfo::getServerInfo()`)
* `getDatabaseInfo` to get database info (`Larinfo::getDatabaseInfo()`)

### Artisan Command

You also can check using `larinfo` artisan command, by running this command below.

```shell
php artisan larinfo
```

The example of the result is shown below.

```
Larinfo
=======

+--------------------+------------------------------------------+
| Application                                                   |
+--------------------+------------------------------------------+
| PHP version        | 8.0.3                                    |
| Laravel version    | 8.35.1                                   |
+--------------------+------------------------------------------+
| Database                                                      |
+--------------------+------------------------------------------+
| Engine             | MySQL                                    |
| Version            | 8.0.22                                   |
+--------------------+------------------------------------------+
| Operating System                                              |
+--------------------+------------------------------------------+
| Type               | MacOS                                    |
| Name               | MacOS 10.15.7                            |
| Architecture       | x86_64                                   |
| Kernel Version     | 19.6.0                                   |
+--------------------+------------------------------------------+
| Uptime                                                        |
+--------------------+------------------------------------------+
| Uptime             | 2 days, 12 hours, 13 minutes, 43 seconds |
| First Boot         | 2021-04-02 15:27:54                      |
+--------------------+------------------------------------------+
| Server                                                        |
+--------------------+------------------------------------------+
| IP Address         | 104.20.8.94                              |
| Private IP Address |                                          |
| Hostname           | mue-88-130-49-204.dsl.cloudflare.com     |
| Provider           | AS13335 Cloudflare, Inc.                 |
| City               | San Francisco                            |
| Region             | California                               |
| Country            | US                                       |
+--------------------+------------------------------------------+
| Timezone                                                      |
+--------------------+------------------------------------------+
| Application        | Asia/Jakarta                             |
| Server Location    | America/Los_Angeles                      |
+--------------------+------------------------------------------+
| Hardware                                                      |
+--------------------+------------------------------------------+
| Model              | Apple device                             |
| CPU count          | 4                                        |
| CPU                | Intel® Core™ i5-3210M CPU @ 2.50GHz      |
+--------------------+------------------------------------------+
| RAM                                                           |
+--------------------+------------------------------------------+
| Total              | 8.0 GiB                                  |
| Free               | 15.0 MiB                                 |
+--------------------+------------------------------------------+
| SWAP                                                          |
+--------------------+------------------------------------------+
| Total              | 2.0 GiB                                  |
| Free               | 406.5 MiB                                |
+--------------------+------------------------------------------+
| Disk Space                                                    |
+--------------------+------------------------------------------+
| Total              | 2.7 TiB                                  |
| Free               | 1.7 TiB                                  |
+--------------------+------------------------------------------+
```

## License

Please see [License File](LICENSE.md) for more information.