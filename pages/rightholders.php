<?php
require($_SERVER['DOCUMENT_ROOT'].'/private/config.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/mysql.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/memcache.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/session.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/init/var.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/func.php');
require($_SERVER['DOCUMENT_ROOT'].'/private/auth.php');

$var['title'] = 'Для правообладателей';
$var['page'] = 'rightholders';

require($_SERVER['DOCUMENT_ROOT'].'/private/header.php');
?>

<style>
.day {
    background: #4a4a4a;
    text-align: left;
    margin: 25px 0 10px 0;
    padding-left: 6px;
    height: 30px;
    font-size: 13pt;
    line-height: 30px;
    border-radius: 3px;
    color: white;
}

.teamleft{
	float:left; margin-left: 6px;
}

.teamright{
	float:right; margin-right: 6px;
}

</style>

<div class="news-block">
    <div class="news-body">
        <h1>Для правообладателей</h1>
        <p><a name="О-сервисе"></a></p>
        <div class="day">О сервисе</div>
        <p>Сервис anilibria.tv - это библиотека любительских переводов/озвучек, где пользователи могут предоставить свой перевод и озвучку аниме, получая возможность загружать, смотерть и делится ею с другими пользователями проекта. Любой желающий может подать заявку на озвучку и/или перевод.</p>
        <p><a name="Защита-авторских-прав"></a></p>
        <div class="day">Защита авторских прав</div>
        <p>Мы соблюдаем Закон о защите авторских прав в цифровую эпоху (DMCA) и другие действующие законы об авторских правах и быстро удаляем неправомерно размещенный контент, получив соответствующее уведомление о нарушении прав.</p>
        <p><a name="Уведомление-о-нарушении-авторских-прав"></a></p>
        <div class="day">Уведомление о нарушении авторских прав</div>
        <p>Если вы считаете, что контент на сайте anilibria.tv нарушает ваши авторские права, вы можете написать уведомление о нарушении прав и отправить его на адрес нашего агента&nbsp;<a href="mailto:anilibria.dev@gmail.com" class="email">anilibria.dev@gmail.com</a>&nbsp;и сервис anilibria.tv незамедлительно осуществит блокировку перечисленного в уведомлении контента.</p>
        <p>Часть 512(c) Акта об авторских правах (Digital Millennium Copyright Act) предъявляет определенные требования к форме этого уведомления. В соответствии с этим, оно должно быть предоставлено в письменном виде и обязательно содержать следующую информацию:</p>
        <ul>
            <li>Материальную или электронную подпись лица, имеющего право представлять интересы человека, чьи авторские права на материал были нарушены.</li>
            <li>Указание на работы (материалы), в отношении которых нарушены права. В случае, когда уведомление сообщает о нарушениях авторского права на несколько таких материалов (текстов, изображений), в нем должны быть приведены указания на каждый из них.</li>
            <li>Указание на материал, нарушающий авторские права или являющийся субъектом поведения, нарушающего авторские права,&nbsp;каковой&nbsp;материал должен быть удален с серверов anilibria.tv или заблокирован для доступа. Вы также должны предоставить исходный адрес (URL) этого материала.</li>
            <li>Информация о том, как связаться с вами, например адрес электронной почты, почтовый адрес или номер телефона.</li>
            <li>Утверждение, что вы действительно считаете, что использование данного материала в таком виде не разрешено ни владельцем авторских прав, ни его агентом, ни законодательством США.</li>
            <li>Заявление, сделанное ввиду возможных штрафных санкций за предоставление ложных сведений, что представленные вами факты не содержат ошибок и искажений, и вы имеете право действовать от лица владельца авторских прав.</li>
        </ul>
        <p><a name="Удаление-или-блокирование-доступа-к-материалу"></a></p>
        <div class="day">Удаление или блокирование доступа к материалу</div>
        <p>После получения указанного выше уведомления о нарушнии в течение 3 (трех) рабочих дней сервис anilibria.tv либо предпринимает меры по ограничению доступа к спорным материалам, либо, если информации в требовании недостаточно или существуют сомнения в полномочиях заявителя, связывается с лицом, направившим уведомление, для получения дополнительной информации.</p>
        <p>При определенных обстоятельствах anilibria.tv прекращает предоставление сервиса владельцам аккаунтов, которые были замечены в многократных нарушениях авторских прав.</p>
        <p><a name="Встречное-уведомление"></a></p>
        <div class="day">Встречное уведомление</div>
        <p>Если после блокировки контента на основании уведомления о нарушении прав, пользователь имеет претензии относительно такой блокировки и не согласен с ней и при этом считает себя владельцем прав на загруженный им, но заблокированный контент, такой пользователь имеет право прислать Встречное уведомление на адрес <a href="mailto:anilibria.dev@gmail.com" class="email">anilibria.dev@gmail.com</a>, после чего доступ к заблокированному контенту может быть открыт.</p>
        <p>Часть 512(g) Акта об авторских правах требует, чтобы ваше встречное уведомление было предоставлено в письменном виде и обязательно включало в себя следующие элементы:</p>
        <ul>
            <li>Вашу материальную или электронную подпись.</li>
            <li>Указание на материал, который был удален или заблокирован для доступа, и исходный адрес (URL), по которому этот материал был опубликован до того, как в его отношении были предприняты меры.</li>
            <li>Заявление, что понимая ответственность за мошенничество, установленную законодательством Соединённых Штатов, вы добросовестно утверждаете, что материал был удален или заблокирован в результате ошибки или его неправильной идентификации.</li>
            <li>Ваше имя, адрес и номер телефона.</li>
        </ul>
        <p>Учтите, что часть 512(f) Акта об авторских правах указывает, что лицо, которое намеренно высылает ложную информацию о том, что материал был удален или заблокирован по ошибке или из-за неправильной идентификации, может быть привлечено к ответственности.</p>
        <p>&nbsp;</p>
        <hr size="1" noshade="noshade">
        <p>&nbsp;</p>
        <p>It is our policy to respond to notices of alleged infringement that comply with the Digital Millennium Copyright Act (the text of which can be found at the U.S. Copyright Office Web Site,&nbsp;<a href="http://lcweb.loc.gov/copyright/" class="external">http://lcWeb.loc.gov/copyright/</a>) and other applicable intellectual property laws, which may include removing or disabling access to material claimed to be the subject of infringing activity.</p>
        <p>If you are a copyright owner and believe that any User Submission or other content infringes upon your copyrights, you may submit a notification pursuant to the Digital Millennium Copyright Act ("DMCA") by providing our Copyright Agent with the following information in writing (see 17 U.S.C 512(c)(3) for further detail):</p>
        <ul>
            <li>A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed;</li>
            <li>Identification of the copyrighted work claimed to have been infringed, or, if multiple copyrighted works at a single online site are covered by a single notification, a representative list of such works at that site;</li>
            <li>Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled and information reasonably sufficient to permit the service provider to locate the material;</li>
            <li>Information reasonably sufficient to permit the service provider to contact you, such as an address, telephone number, and, if available, an electronic mail;</li>
            <li>A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law; and</li>
            <li>A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.</li>
        </ul>
        <p>anilibria.tv's Copyright Agent to receive notifications of claimed infringement is:&nbsp;<a href="mailto:anilibria.dev@gmail.com" class="email">anilibria.dev@gmail.com</a>. You acknowledge that if you fail to comply with all of the requirements of this Section 5(D), your DMCA notice may not be valid.</p>
	</div>
	<div class="clear"></div>
	<div style="margin-top:10px;"></div>
</div>


<?php require($_SERVER['DOCUMENT_ROOT'].'/private/footer.php');?>
