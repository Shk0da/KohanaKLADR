# kohana-kladr
����������� ����� � www.gnivc.ru

����������: ��� ������ ����� ��������� � php.ini extension=dbase.so

###�������������:
*1.* � application\bootstrap.php ����������:

    Kohana::modules(array(
        ...
        'kladr' => MODPATH . 'kladr',
    ));

*2.* ������� ������ � �����:

    Cron::set('update_kladr', array('* * * 3 *', function () {
        $kladr = new Kladr();
        $kladr->update();
    }), 'default, update_kladr');
 
 �������� ��������������� ���������� ������ =)