<br /><br />
{include file="$template/includes/pageheader.tpl" title="Login with Digi-ID"}

{include file="$template/includes/alert.tpl" idname="digiidStatus" type="error" hide="true" msg="DigiID verification Sucess, but no useraccount connected." textcenter=true}

<div align="center" id="DigiIDLogin">
    <a href='{$DigiIDURL}' id='digiidlink'>
        <img alt='digiid login' title='Digi-ID Login' />
    </a>
</div>

<script type="text/javascript" src="modules/addons/digiid/js.js"></script>