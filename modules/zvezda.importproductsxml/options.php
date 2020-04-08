<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin.php");
CJSCore::Init(array("jquery3"));
CUtil::InitJSCore(array('window'));
IncludeModuleLangFile(__FILE__);

if(!$USER->IsAdmin())
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

// получаем настройки

?>
<form action="">

    <div class="steps ">

        <div class="step" data-action="options">
            В будущем здесь блок настроек. После первого заполнения скрыт по умолчанию
        </div>

        <div class="step active get-file">
            <div class="step__title">ШАГ 1: выбор файла</div>
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
                <a href="#" class="button next">далее</a>
            </div>
        </div>

        <div class="step" data-action="set_sections">
            <div class="step__title">ШАГ 2: Выбор разделов и ИБ куда грузить</div>
            <table>
                <thead>
                <tr>
                    <th>Раздел в файле</th>
                    <th>Загружать в</th>
                </tr>
                </thead>
                <tbody id="sections">
                </tbody>
            </table>

            <div class="buttons">
                <a href="#" class="button prev">назад</a>
                <a href="#" class="button next">далее</a>
            </div>


        </div>

    </div>

</form>


<?php


?>

<script>

    let module_path = "/local/modules/zvezda.importproductsxml/tools/";
    let file_path_remote;
    let file_path_local;
    let file_remote_status;

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

        getFileStatus();


        stepGetFile(file_url, next_step);

    });

    // $('.button.next').on('click', function () {
    //
    // })
    $('.button.prev').on('click', function () {
        let current_step = $(this).closest('.step');
        let prev_step = current_step.prev('.step');
        // let action = prev_step.attr('data-action');

        current_step.removeClass('active');
        prev_step.addClass('active');

        // if( action == "set_sections" )
        // {
        //     showSectionsFromFile();
        // }
    });

    $('#sections').on( 'click', '.js_addlevel', function(){
        let container = $(this).closest('td');
        let parent = container.parent();
        let iblock_id = "";
        let section_id = "";
        let select_container;

        console.log(parent);

        if( parent.attr('data-iblock-id') != undefined ){
            iblock_id = parent.attr('data-iblock-id');
        }
        if( parent.attr('data-section-id') != undefined ){
            section_id = parent.attr('data-section-id');
        }

        $(this).detach();
        container.append('<select class="show_select"></select>');
        select_container = container.find('.show_select');
        showSectionsFromIblock(select_container, iblock_id, section_id);
        container.append('<div class="js_applay">applay</div>');
    });

    $('#sections').on( 'click', '.js_applay', function(){
        let container = $(this).closest('td');
        applay(container);
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


    function showFileStatus(){
        // глушим экран
        // проверяем доступность файла
        // Выводим сообщение
        // открывваем экран
        // активируем кнопку "Далее"
        $.ajax({
            method: "POST",
            url: module_path + "getFileStatus.php",
            dataType: 'json',
            data:{file_path:file_path_remote}
            success: function(data){
                // $.each(data, function(index,value){
                //     $('#shop-file').append('<option value="'+value['FILE_REFERENCE']+'">'+value['NAME']+'</option>');
                // });
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

    function stepGetFile( file_path ){
        file_path_remote = file_path;
        console.log(file_path_remote);

        //Показываем плейсхолдер, ждём проверки доступности файла
        // Если ок - продолжаем, если нет - возвращаем на первый шаг

        // здесь проверяем доступность файла, сохраняем файл к нам, записываем ссылку на него для передачи др методам, пока не доавляем во временную таблицу - это после старта
        // file_path_local; - сюда записываем путь к скачанному файлу, пока дублируем удалённый
        file_path_local = file_path_remote;

        showSectionsFromFile( contest ); // Сюда вставляем нашу копию

    }

    function remove( context ) {
        context.nextAll('td').detach();
        addLevel( context.closest('tr') );
        context.find('.js_remove').detach();
        container.append('<div class="js_applay">applay</div>');

    }

    function applay( context ) {

        let value = context.find('select').val();
        // let name = context.find('select').text();
        if( !context.parent().attr('data-iblock-id') ){
            context.parent().attr('data-iblock-id', value);
        }
        else{
            context.parent().attr('data-section-id', value);
        }

        context.find('.js_applay').remove();
        context.find('.select').attr('disabled', 'disabled');
        context.append('<div class="js_remove">remove</div>');
        addLevel( context.closest('tr') );
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

    function showSectionsFromFile( context ){

        if( file_path_local != '' ){
            $.ajax({
                method: "POST",
                url: module_path + "ajaxGetSectionsFromFile.php",
                dataType: 'json',
                context: context,
                data:{file_path:file_path_local},
                success: function(data){
                    // TODO отработать статус data['STATUS']
                    // TODO Показать сообщение data['MASSAGE']

                    // TODO визуализировать многоуровневость
                    $.each( data['SECTIOMS'], function(index,value){
                        context.find('#sections').append('<tr><td><div data-section-id="'+value['ID']+'" >'+value['NAME']+'</div></td><td><a class="js_addlevel" href="#">Добавить уровень</a></td></tr>');
                    });
                },
                error: function(response){
                    // $("#result #error").show();
                    // setTimeout('$("#result #error").hide()', 5000);
                }
            })
        }
    }

    function showSectionsFromIblock( context, iblock, parrent ){
        console.log(iblock);
        console.log(parrent);
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