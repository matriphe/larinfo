# Larinfo

[![Larinfo](https://github.com/matriphe/larinfo/actions/workflows/master.yml/badge.svg)](https://github.com/matriphe/larinfo/actions/workflows/master.yml)
[![Total Download](https://img.shields.io/packagist/dt/matriphe/larinfo.svg)](https://packagist.org/packages/matriphe/larinfo)
[![Latest Stable Version](https://img.shields.io/packagist/v/matriphe/larinfo.svg)](https://packagist.org/packages/matriphe/larinfo)

Larinfo provide system information for Laravel. 

It wraps [Linfo](https://github.com/jrgp/linfo) to show IP address information on the server and client side, server software versions, and hardware information.

## Requirements

- **PHP version**: `^8.1`.
- **Laravel version**: `^10.0`.

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
- Laravel `^9.0` **[please use version 4.0.0](https://github.com/matriphe/larinfo/releases/tag/4.0.0)** by running `composer require matriphe/larinfo:4.0.0`.

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
| PHP version        | 8.1.12                                   |
| Laravel version    | 10.4.1                                   |
+--------------------+------------------------------------------+
| Database                                                      |
+--------------------+------------------------------------------+
| Engine             | SQLite                                   |
| Version            | 3.40.0                                   |
+--------------------+------------------------------------------+
| Operating System                                              |
+--------------------+------------------------------------------+
| Type               | MacOS                                    |
| Name               | MacOS 13.2.1                             |
| Architecture       | arm64                                    |
| Kernel Version     | 22.3.0                                   |
+--------------------+------------------------------------------+
| Uptime                                                        |
+--------------------+------------------------------------------+
| Uptime             | 5 days, 13 hours, 38 minutes, 25 seconds |
| First Boot         | 2023-03-19 22:36:46                      |
+--------------------+------------------------------------------+
| Server                                                        |
+--------------------+------------------------------------------+
| IP Address         | 79.224.166.123                           |
| Private IP Address |                                          |
| Hostname           | p5fe9ab9c.dip0.t-ipconnect.de            |
| Provider           | AS3320 Deutsche Telekom AG               |
| City               | Berlin                                   |
| Region             | Berlin                                   |
| Country            | DE                                       |
+--------------------+------------------------------------------+
| Timezone                                                      |
+--------------------+------------------------------------------+
| Application        | UTC                                      |
| Server Location    | Europe/Berlin                            |
+--------------------+------------------------------------------+
| Hardware                                                      |
+--------------------+------------------------------------------+
| Model              | Mac mini                                 |
| CPU count          | 8                                        |
| CPU                | Apple M1                                 |
+--------------------+------------------------------------------+
| RAM                                                           |
+--------------------+------------------------------------------+
| Total              | 16.0 GiB                                 |
| Free               | 54.0 MiB                                 |
+--------------------+------------------------------------------+
| SWAP                                                          |
+--------------------+------------------------------------------+
| Total              | 3.0 GiB                                  |
| Free               | 964.7 MiB                                |
+--------------------+------------------------------------------+
| Disk Space                                                    |
+--------------------+------------------------------------------+
| Total              | 2.2 TiB                                  |
| Free               | 1.0 TiB                                  |
+--------------------+------------------------------------------+
```

## Running Tests

To run the unit tests, execute this following command.

```shell
vendor/bin/phpunit --group unit
```

On the GitHub Actions, the tests run on the respective operating system, which are `ubuntu`, `macos`, and `windows`.

To run the tests on [**Ubuntu 22.04 (Jammy)**](https://github.com/actions/runner-images/blob/23ff0d746804fc3c0ac7f961f2fbca953824c775/images/linux/Ubuntu2204-Readme.md), run this following command.

```shell
vendor/bin/phpunit --group ubuntu
```

To run the tests on [**macOS Big Sur (11)**](https://github.com/actions/runner-images/blob/23ff0d746804fc3c0ac7f961f2fbca953824c775/images/macos/macos-11-Readme.md) and [**macOS Monterey (12)**](https://github.com/actions/runner-images/blob/23ff0d746804fc3c0ac7f961f2fbca953824c775/images/macos/macos-12-Readme.md) on x86_64, run this following command.

```shell
vendor/bin/phpunit --group macos
```

To run the tests on [**Windows Server 2019)**](https://github.com/actions/runner-images/blob/23ff0d746804fc3c0ac7f961f2fbca953824c775/images/win/Windows2019-Readme.md), run this following command.

```shell
vendor/bin/phpunit --group windows
```

## License

Please see [License File](LICENSE.md) for more information.