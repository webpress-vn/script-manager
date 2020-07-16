<?php

namespace VCComponent\Laravel\Script\Services;

use Illuminate\Support\Facades\Cache;

class Script
{
    protected $data         = [];
    protected $getScripts   = ['head', 'beforebody', 'afterbody'];
    protected $cache        = false;
    protected $cacheMinutes = 60;

    public function __construct()
    {
        $this->data = collect($this->data);
        foreach ($this->getScripts as $script) {
            $this->data = $this->data->push(['position' => $script, 'content' => null, 'fetched' => false]);
        }

        if (config('script.script_key') !== []) {
            foreach (config('script.script_key') as $key) {
                $this->data = $this->data->push(['position' => $key, 'content' => null, 'fetched' => false]);
            }
        }

        if (config('script.cache')['enabled'] === true) {
            $this->cache     = true;
            $this->timeCache = config('script.cache')['minutes'] ? config('script.cache')['minutes'] * 60 : $this->cacheMinutes * 60;
        }
    }

    public function get_Script($position)
    {
        $position = strtolower($position);
        $position = trim($position);

        $found = $this->data->search(function ($i) use ($position) {
            return $i['position'] === $position;
        });

        if (!$found) {
            $this->prepare($position);
            $this->fetch();
            $item = $this->data->search(function ($i) use ($position) {
                return $i['position'] === $position;
            });
            $result = $this->data->get($item);

            return $result['content'];

        } else {
            $item = $this->data->get($found);
            if ($item['fetched'] === true) {
                return $item['content'];
            } else {
                $this->fetch();
                return $item['content'];
            }
        }
    }

    public function prepare($position)
    {
        $data_keys = $this->data->pluck('position');
        $diff_keys = collect($position)->diff($data_keys);
        if ($diff_keys->count()) {
            $this->data = $this->data->push(['position' => $diff_keys->first(), 'content' => null, 'fetched' => false]);
        }
        return '';
    }

    public function fetch()
    {
        if ($this->cache === true) {
            if (Cache::has('scriptFetched') && Cache::get('scriptFetched')->count() !== 0) {
                return Cache::get('scriptFetched');
            }
            return Cache::remember('scriptFetched', $this->timeCache, function () {
                return $this->fetchExcute();
            });
        }
        return $this->fetchExcute();
    }

    public function fetchExcute()
    {
        $un_fetched = $this->data->filter(function ($value, $key) {
            return $value['fetched'] == false;
        });

        if ($un_fetched->count()) {
            $items      = \VCComponent\Laravel\Script\Entities\Script::select('position', 'content')->whereIn('position', $un_fetched->pluck('position'))->get();
            $this->data = $this->data->map(function ($d) use ($items) {
                $found = $items->search(function ($i) use ($d) {
                    return $i->position === $d['position'];
                });
                if ($found !== false) {

                    return [
                        'position' => $items->get($found)->position,
                        'content'  => $items->get($found)->content,
                        'fetched'  => true,
                    ];
                } else {
                    return $d;
                }
            });

            $this->data = $this->data->map(function ($item) {
                if ($item['fetched'] === false) {
                    return [
                        'position' => $item['position'],
                        'content'  => null,
                        'fetched'  => true,
                    ];
                } else {
                    return $item;
                }
            });
        }

        return $this->data;
    }
}
