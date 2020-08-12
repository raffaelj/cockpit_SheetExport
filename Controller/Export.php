<?php

namespace SheetExport\Controller;

class Export extends \Cockpit\AuthController {

    public function index($collection = null) {}

    public function export($collection = null, $type = 'json') {

        if (!$this->app->module('collections')->hasaccess($collection, 'entries_view')) {
            return $this->helper('admin')->denyRequest();;
        }

        $collection   = $collection ? $collection : $this->app->param('collection', '');
        $options = $this->app->param('options', []);
        $type    = $this->app->param('type', $type);

        $collection = $this->module('collections')->collection($collection);

        if (!$collection) return false;

        $this->app->trigger('collections.export.before', [$collection, &$type, &$options]);

        switch($type) {
            case 'json' : return $this->json($collection, $options);           break;
            case 'csv'  : return $this->sheet($collection, $options, 'Csv');            break;
            case 'ods'  : return $this->sheet($collection, $options, 'Ods');   break;
            case 'xls'  : return $this->sheet($collection, $options, 'Xls');   break;
            case 'xlsx' : return $this->sheet($collection, $options, 'Xlsx');  break;
            default     : return false;
        }

    }

    protected function json($collection, $options) {

        $entries = $this->module('collections')->find($collection['name'], $options);

        $this->app->response->mime = 'json';

        return \json_encode($entries, JSON_PRETTY_PRINT);

    } // end of json()

    protected function sheet($collection = [], $options = [], $type = 'Ods') {

        $user = $this->app->module('cockpit')->getUser();

        $filename = $collection['name'];

        $description = "Exported with Cockpit SheetExport addon";

        $prettyTitles = $options['pretty'] ?? false;

        if (!empty($collection['description']))
            $description .= "\r\n\r\n" . $collection['description'];

        if (!empty($options))
            $description .= "\r\n\r\nUser defined filter options:\r\n";

        foreach ($options as $key => $val) {
            $description .= $key . ': ' . json_encode($val) . "\r\n";
        }

        $opts = [
            'title'       => !empty($collection['label']) ? $collection['label'] : $collection['name'],
            'creator'     => !empty($user['name']) ? $user['name'] : $user['user'],
            'description' => trim($description),
        ];

        $spreadsheet = new \SheetExport($opts);

        // quick fix to enable _id
        $collection['fields'][] = [
            'name' => '_id',
        ];

        // table headers
        $c = 'A';
        $r = '1';
        foreach($collection['fields'] as $field) {

            if (empty($options['fields']) ||
                !$this->module('collections')
                    ->is_filtered_out($field['name'], $options['fields'], '_id'))
            {
                // $spreadsheet->setCellValue($c.$r, $field['name']);
                $spreadsheet->setCellValue($c.$r, $prettyTitles && !empty($field['label']) ? $field['label'] : $field['name']);
                $c++;
            }
        }

        // table contents
        $entries = $this->module('collections')->find($collection['name'], $options);

        $c = 'A';
        $r = '2';
        foreach($entries as $entry) {

            foreach($collection['fields'] as $field) {

                if (isset($entry[$field['name']]) && is_array($entry[$field['name']])) {
//                     $entry[$field['name']] = implode(', ', $entry[$field['name']]); // nice for tags, but fails for e. g. gallery
                    $entry[$field['name']] = json_encode($entry[$field['name']]);
                }

                if (empty($options['fields']) ||
                    !$this->module('collections')
                        ->is_filtered_out($field['name'], $options['fields'], '_id'))
                {
                    $spreadsheet->setCellValue($c.$r, $entry[$field['name']] ?? '');
                    $c++;
                }

            }

            $c = 'A';
            $r++;

        }

        // write file and exit
        $spreadsheet->write($type, $filename);

    } // end of sheet()

}
