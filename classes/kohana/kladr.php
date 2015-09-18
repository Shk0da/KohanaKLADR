<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Kladr
{

    protected $config = [];

    private $archive_kladr;
    private $dbf = 'KLADR.DBF';
    private $dbf_street = 'STREET.DBF';
    private $dbf_house = 'DOMA.DBF';
    private $dbf_socr = 'SOCRBASE.DBF';

    public function __construct()
    {
        $this->config = Kohana::$config->load('kladr')->as_array();

        if ( !is_dir($this->config['tmp_folder']) )
            mkdir($this->config['tmp_folder'], 0777, true);
    }

    public function update()
    {
        try {
            $this->logger("Начало импорта: ");
            $this->unset_old();
            $this->download_kladr();
            $this->unzip_kladr();
            $this->import_data_to_db();
            $this->logger("Импорт закончен. \n\n");
        } catch (Exception $e) {
            $this->logger("Импорт завершился ошибкой: \n\n" . $e);
        }
    }

    private function logger($event, $error = false)
    {
        $log = new Log_File($this->config['tmp_folder']);
        $message['time'] = date('Y-m-d H:i:s');
        $message['body'] = $error ? '!!!ERROR!!! - ' : 'OK - ' . $event;
        $log->write([$message]);
    }

    private function unset_old()
    {
        $this->logger('Удаляем старые файлы');

        foreach (glob($this->config['tmp_folder'] . '*.*') as $file)
        {
            unlink($file) ? $this->logger('-- удален ' . $file)
                : $this->logger('Не удалось удалить: ' . $file, true);
        }
    }

    private function download_kladr()
    {
        if ( !$this->get_filesize($this->config['link_to_kladr']) )
        {
            $this->logger(' URL не найден: ' . $this->config['link_to_kladr'], true);
            exit;
        }

        $local_dir = $this->config['tmp_folder'];
        $info = pathinfo($this->config['link_to_kladr']);
        $filename = $info['basename'];
        $this->archive_kladr = $local_dir . $filename;

        $this->logger('Скачали архив сюда: ' . $this->config['link_to_kladr'] . ' to ' . $this->archive_kladr);
        exec('wget ' . $this->config['link_to_kladr'] . ' -O ' . $this->archive_kladr);
    }

    private function get_filesize($url)
    {
        if ( preg_match('/^http\:\/\//', $url) )
        {
            $x = array_change_key_case(get_headers($url, 1), CASE_LOWER);
            if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') != 0 )
                return $x['content-length'][1];
            else
                return $x['content-length'];
        } else return 0;
    }

    private function unzip_kladr()
    {
        $this->logger('Начали распаковку архива ' . $this->archive_kladr);
        exec('7z e ' . $this->archive_kladr . ' -o' . $this->config['tmp_folder']);
        $this->logger('Распаковка ' . $this->archive_kladr . ' завершена');
    }

    private function import_data_to_db()
    {
        $this->logger('Импортируем КЛАДР');
        $model_kladr = new Model_Kladr();
        $this->import_data($this->config['tmp_folder'] . $this->dbf, $model_kladr);

        $this->logger('Импортируем улицы');
        $model = new Model_Kladr_Street();
        $this->import_data($this->config['tmp_folder'] . $this->dbf_street, $model);

        $this->logger('Импортируем дома');
        $model = new Model_Kladr_Doma();
        $this->import_data($this->config['tmp_folder'] . $this->dbf_house, $model);

        $this->logger('Импортируем сокр.');
        $model = new Model_Kladr_Socr();
        $this->import_data($this->config['tmp_folder'] . $this->dbf_socr, $model);

        $this->logger('Отправлен на выполнение корректирование');
        DB::update($model_kladr->get_name())
            ->set(['status' => '2'])
            ->where('code', '=', '7200000100000')
            ->or_where('code', '=', '5800000100000')
            ->execute();

        DB::update($model_kladr->get_name())
            ->set(['status' => '22235000000'])
            ->where('code', '=', '5202500000000')
            ->execute();

        DB::update($model_kladr->get_name())
            ->set(['status' => '97253000000'])
            ->where('code', '=', '2102000000000')
            ->execute();

    }

    private function import_data($file, Model_Kladr $model)
    {
        $dbf = dbase_open($file, 2);
        if ( !$dbf )
        {
            $this->logger('Не получилось открыть dbf файл ' . $file, true);
            exit;
        }

        $records = dbase_numrecords($dbf);
        $structure = $model->get_structure();
        DB::query(NULL, 'TRUNCATE TABLE ' . $model->get_name())->execute();

        for ($i = 1; $i <= $records; $i++)
        {
            $data = dbase_get_record_with_names($dbf, $i);
            $row = [];

            foreach ($structure as $key)
            {
                $row[] = mb_convert_encoding($data[$key], 'utf-8', 'cp-866');
            }

            $sql_rows[] = $row;
            if ( count($sql_rows) > 350 || $i == $records )
            {
                $this->insert_data($model, $sql_rows);
                $sql_rows = [];
            }
        }

        dbase_close($dbf);
    }

    private function insert_data(Model_Kladr $model, $data)
    {
        if ( !$data )
        {
            $this->logger('Не получилось сделать запись в ' . $model->get_name(), true);
            exit;
        }

        foreach ($data as $value)
        {
            $value = array_map('trim', $value);
            $value = array_map('mysql_real_escape_string', $value);

            ORM::factory($model->get_model_name())
                ->values(array_combine(array_flip($model->get_structure()), $value))
                ->save();
        }

    }
}