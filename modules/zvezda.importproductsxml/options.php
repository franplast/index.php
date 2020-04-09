<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
CJSCore::Init(array("jquery3"));
CUtil::InitJSCore(array('window'));
IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin())
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// получаем настройки

?>
<style>
    .content-wrapper {
        background-color: white;
        padding: 20px 30px;
    }
    .summ-info {
        border: #f2dede solid 2px;
        background-color: white;
        padding: 10px 20px;
        margin-bottom: 30px;
    }
    .step .content-block {
        display: none;
    }
    .step.active .content-block {
        display: block;
    }

    #sections ul {
        list-style: none;
        padding-left: 20px;
        padding-top: 4px;
    }
    #sections li {
        padding-top: 6px;
        margin-bottom: 6px;
    }

    .death-level-1 ul {display: none;}

    .show-sublevel{
        cursor: pointer;
    }
    .show-sublevel:before {
        content: ">";
        padding-right: 4px;
    }

    .buttons {display: flex;}
    .button {
        display: block;
        padding: 5px 11px;
        border: #3ac769 solid 1px;
        border-radius: 5px;
        font-size: 18px;
        margin: 5px;
        cursor: pointer;
        color: white;
        background-color: green;
        text-decoration: none;
    }
    .button a,
    .button a:hover,
    {
        color: inherit;
        text-decoration: none;
    }
    .button:hover {text-decoration: none; background-color: #00be00; }
    .button.button-red{background-color: red;}
    .button.button-red:hover{background-color: #ff4508;}


    .bind {
        display: inline-flex;
        align-items: center;
    }

    .show_select {
        display: flex;
        align-items: center;
    }

</style>

<div class="content-wrapper">

    <section id="summ-info" class="summ-info">
        <h2>Общая информация</h2>

    </section>
    <form action="">

        <div class="steps ">

            <div class="step" data-action="options">
                <h3 class="step__title">ШАГ 0: Общие настройки</h3>
                <div class="content-block">
                    В будущем здесь блок настроек. После первого заполнения скрыт по умолчанию
                </div>
            </div>

            <section class="step get-file">
                <h3 class="step__title">ШАГ 1: выбор файла</h3>
                <div class="content-block"  style="display: block">
                    <fieldset>
                        <legend>Укажите источник</legend>
                        <div class="field-group">
                            <label for="shop-file">Выберите из магазинов:</label>
                            <select id="shop-file" name="file" >
                                <option selected disabled>  Выберите...</option>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="manual-file">Или укажите вручную:</label>
                            <input id="manual-file" type="text"  name="file" />
                        </div>
                    </fieldset>

                    <div class="buttons">
                        <a href="#" class="button prev">назад</a>
                        <a href="#" class="button next">далее</a>
                    </div>
                </div>
            </section>

            <section class="step get-sections">
                <h3 class="step__title">ШАГ 2: Выбор разделов и ИБ куда грузить</h3>

                <div class="content-block">
                    <div class="warning">
                        ВАЖНО!!! Товары для несопоставленных разделов загружаться не будут
                        НО если раздел не настроен, но раздел выше настрон, будут взяты его натройки
                    </div>
                    <div id="sections" class="sections">
                        <ul class="death-level-1">
                        </ul>
                    </div>
                    <div class="buttons">
                        <a href="#" class="button prev">назад</a>
                        <a href="#" class="button next">далее</a>
                    </div>
                </div>

            </section>

            <section class="step get-props">
                <h3 class="step__title">ШАГ 3: Сопставление св-в</h3>

                <div class="content-block">
                    <div class="warning">
                        Вам нужно повторить сопоставление св-в и полей для каждого выбранноо ИБ и при необходимости для SKU<br>
                        Поля - это стандартные св-ва элементов битрикса, они перечислены на вкладке "Поля" в настройках ИБ<br>
                        Св-ва - это созданные вручную дополнительные св-ва, они перечислены на вкладке "Свойства" в настройках ИБ
                    </div>

                    <ul id="properties" class="properties">

                    </ul>


                    <div class="buttons">
                        <a href="#" class="button prev">назад</a>
                        <a href="#" class="button next">далее</a>
                    </div>
                </div>
            </section>


        </div>
    </form>

</div>
<?php


?>

<script>

    let module_path = "/local/modules/zvezda.importproductsxml/tools/";
    let file_path_remote;
    let file_path_local;
    let file_remote_status;
    let relations_iblocks;

    // getOptions();

    showShops();

    $('.get-file').on('click', '.button.next', function () {
        let current_step = $(this).closest('.step');
        let next_step = current_step.next('.step');
        let shop_file = $('#shop-file').val();
        let manual_file = $('#manual-file').val();
        let file_url;

        if( manual_file != '' ){
            file_url = manual_file;
        }
        else if( shop_file !== null) {
            file_url = shop_file;
        }
        else{
            alert( 'Не выбран файл' );
            return false;
        }
        addInfo( '<div id="file_url">'+file_url+'<span id="file_status"></span></div>' );
        showFileStatus();
        afterStepGetFile(file_url, next_step);
        beforeStepSections(next_step);
        current_step.find('.content-block').hide();
        next_step.find('.content-block').show();

    });

    $('.get-sections').on('click', '.button.next', function () {
        let current_step = $(this).closest('.step');
        let next_step = current_step.next('.step');

        afterStepSections();
        beforeStepProps();
        current_step.find('.content-block').hide();
        next_step.find('.content-block').show();
    });

    $('#sections').on( 'click', '.js_addlevel', function(){
        let container = $(this).closest('.bind');
        let container_select = container.find('.show_select');
        let container_path = container.find('.path');
        let path_cnt =  container_path.find('span').length;
        let iblock_id = "";
        let section_id = "";
        if( path_cnt > 0  )
            iblock_id = container_path.find('span').filter(':first').attr('data-id');
        if( path_cnt > 1 )
            section_id = container_path.find('span').filter(':last').attr('data-id');

        $(this).remove();
        container_select.append('<select></select><div class="button js_apply">v</div><div class="button button-red js_breack">x</div>');
        showSectionsFromIblock(container_select.find('select'), iblock_id, section_id);

    });

    $('#sections').on( 'click', '.js_apply', function(){
        let container = $(this).closest('.bind');
        let id = container.find('select').val();
        let name = container.find('select option:selected').text();

        container.find('.show_select').empty();

        container.find('.path').append('/<span data-id="'+id+'">'+name+'</span>');
        container.append('<a class="js_addlevel" href="#">+</a>');
    });

    $('#sections').on( 'click', '.js_breack', function(){
        let container = $(this).closest('.bind');
        container.find('.show_select').empty();
        container.append('<a class="js_addlevel" href="#">+</a>');
    });

    $('#sections').on( 'click', '.js_remove', function(){
        let container = $(this).closest('td');
        remove(container);
    });

    $('#sections').on( 'click', '.js_choose_sect', function(){
        let container = $(this).closest('td');
        let select_container;

        $(this).detach();
        container.append('<select class="show_select"></select>');
        select_container = container.find('.show_select');
        showSectionsFromIblock(select_container);
        container.append('<div class="js_applay">applay</div>');
    });

    $('#sections').on('click', '.show-sublevel', function () {
        $(this).siblings('ul').slideToggle();
    })


    function afterStepGetFile( file_path ){
        file_path_remote = file_path;
        //Показываем плейсхолдер, ждём проверки доступности файла
        // Если ок - продолжаем, если нет - возвращаем на первый шаг
        // здесь проверяем доступность файла, сохраняем файл к нам, записываем ссылку на него для передачи др методам, пока не доавляем во временную таблицу - это после старта
        // file_path_local; - сюда записываем путь к скачанному файлу, пока дублируем удалённый
        file_path_local = file_path_remote;
    }

    function beforeStepSections(contecst){
        showSectionsFromFile( contecst ); // Сюда вставляем блок шага

    }

    function afterStepSections(){
        // Здесь можно сохранить состояние связей, или можно отложить это на конец.
        // Собираем список использованных ИБ
        let paths = $('#sections').find('.path');
        relations_iblocks = [];
        paths.each(function( index, value) {
            let iblock_id = $(value).find('span:first').attr('data-id')
            if( iblock_id !== undefined )
                relations_iblocks.push(iblock_id);
        });
        console.log(relations_iblocks);

        $('#relations_iblocks').remove();
        addInfo( '<div id="relations_iblocks" class="relations_iblocks"><h4>Для загрузки выбраны ИБ-ки</h4><ul class="content"></ul></div>'  );
        $.ajax({
            method: "POST",
            url: module_path + "beforeStepProps.php",
            dataType: 'json',
            data:{iblock_ids:relations_iblocks},
            success: function(data){
                $.each(data['ITEMS'], function(index,value){
                    addInfo( '<li>'+value['NAME']+' ('+value['NAME']+')</lI>', $('#relations_iblocks').find('.content') );
                });
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }

    function beforeStepProps(){

        if( file_path_local != '' ){
            $.ajax({
                method: "POST",
                url: module_path + "ajaxGetPropsFromFile.php",
                dataType: 'json',
                // context: context,
                data:{file_path:file_path_local},
                success: function(data){
                    // console.log(data['SECTIONS']);
                    // TODO отработать статус data['STATUS']
                    // TODO Показать сообщение data['MASSAGE']
                    // TODO визуализировать многоуровневость
                    let container = $('#properties').find('ul') ;
                    let tmp_death_levl = 1;
                    $.each( data['SECTIONS'], function(index,value){

                        container.append('<li><span>' + value['NAME'] + '</span></li>');
                    });
                    $('#properties').find('ul').siblings('span').addClass('show-sublevel');
                },
                error: function(response){
                    // $("#result #error").show();
                    // setTimeout('$("#result #error").hide()', 5000);
                }
            })
        }

    }

    function afterStepProps(){

    }

    function addInfo( information, target = false ){
        let container = $('#summ-info');
        if( target === false ) target = container;
        target.append( information );
    }

    function showFileStatus(){
        // глушим экран
        // проверяем доступность файла
        // Выводим сообщение
        // открывваем экран

        $.ajax({
            method: "POST",
            url: module_path + "getFileStatus.php",
            dataType: 'json',
            data:{file_path:file_path_remote},
            success: function(data){

                $('#file_status').addClass('status_'+data['STATUS']);
                if( data['STATUS'] === 1 ){
                    $('#file_status').text('файл доступен');
                }
                else if( data['STATUS'] === 0){
                    $('#file_status').text('файл не доступен');
                }
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })

    };

    function getFileStatus(){
        // так как статус получаем в ajax - не можем сразу установить его в переменную
        // Поэтому получаем его на следующем шаге из блока куда его поместил аякс
        if( file_remote_status == '' ){
            // получаем из разметки, если ещё не устаовлен, иначе сразу отдаём установленный
            file_remote_status = 1; // 0 - недоступен, 1 - доступен
        }
        return file_remote_status;
    }

    function remove( context ) {
        context.nextAll('td').detach();
        addLevel( context.closest('tr') );
        context.find('.js_remove').detach();
        container.append('<div class="js_applay">applay</div>');

    }

    function addLevel( context ){
        context.append('<td><a class="js_addlevel">Добавить уровень</a></td>');
    }

    function showShops(){
        $.ajax({
            method: "POST",
            url: module_path + "ajaxGetShops.php",
            dataType: 'json',
            success: function(data){
                $.each(data, function(index,value){
                    $('#shop-file').append('<option value="'+value['FILE_REFERENCE']+'">'+value['NAME']+'</option>');
                });
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }

    function showSectionsFromFile(){

        // TODO дополнить данные сохранёнными связями

        if( file_path_local != '' ){
            $.ajax({
                method: "POST",
                url: module_path + "ajaxGetSectionsFromFile.php",
                dataType: 'json',
                // context: context,
                data:{file_path:file_path_local},
                success: function(data){
                    // console.log(data['SECTIONS']);
                    // TODO отработать статус data['STATUS']
                    // TODO Показать сообщение data['MASSAGE']
                    // TODO визуализировать многоуровневость
                    let container = $('#sections').find('ul') ;
                    let tmp_death_levl = 1;
                    $.each( data['SECTIONS'], function(index,value){
                        if(tmp_death_levl < value['DEPTH_LEVEL']){
                            container = container.find('li').filter( ':last' ).append('<ul class="death-level-' + value['DEPTH_LEVEL'] + '"></ul>').find('ul');
                            tmp_death_levl = value['DEPTH_LEVEL'];
                        }
                        else if( tmp_death_levl > value['DEPTH_LEVEL'] ){
                            container = container.closest('.death-level-' + value['DEPTH_LEVEL']);
                            tmp_death_levl = value['DEPTH_LEVEL'];
                        }
                        container.append('<li class="death-'  + tmp_death_levl + '" data-filesectionid="'+value['ID']+'"><span>' + value['NAME'] + '</span> <div class="bind"><div class="path"></div><div class="show_select"></div><a class="js_addlevel" href="#">+</a></div></li>');
                    });
                    $('#sections').find('ul').siblings('span').addClass('show-sublevel');
                },
                error: function(response){
                    // $("#result #error").show();
                    // setTimeout('$("#result #error").hide()', 5000);
                }
            })
        }
    }

    function showSectionsFromIblock( context, iblock, parrent ){
        // console.log(iblock);
        // console.log(parrent);
        $.ajax({
            method: "POST",
            url: module_path + "ajaxGetSectionsFromIblock.php",
            dataType: 'json',
            context: context,
            data:{ iblock:iblock, parrent:parrent },
            success: function(data){
                if( data.length == 0 ){
                    context.append('<option>Глубже некуда</option>');
                }
                else
                {
                    $.each(data, function(index,value){
                        context.append('<option value="'+value['ID']+'">'+value['NAME']+'</option>');
                    });
                }
            },
            error: function(response){
                // $("#result #error").show();
                // setTimeout('$("#result #error").hide()', 5000);
            }
        })
    }





</script>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");