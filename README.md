# kohana-kladr
Импортирует КЛАДР с www.gnivc.ru

Примечание: для работы нужно подрубить в php.ini extension=dbase.so

###Использование:
*1.* В application\bootstrap.php подключаем:

    Kohana::modules(array(
        ...
        'kladr' => MODPATH . 'kladr',
    ));

*2.* Создаем задачу в кроне:

    Cron::set('update_kladr', array('* * * 3 *', function () {
        $kladr = new Kladr();
        $kladr->update();
    }), 'default, update_kladr');
 
 Радуемся ежеквартальному обновлению КЛАДРа =)