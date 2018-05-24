<div class="digiSetup alert alert-update-banner alert-update-banner-success{if !$digiidAddress} hidden{/if}"">
    <div class="alert-icon">
        <i class="fa fa-check"></i>
    </div>
    <div class="alert-msg">
        <strong>You're good to go!</strong><br>
        Digi-ID wallet <i>{$digiidAddress}</i> is linked to your account.
    </div>
    <span id="digiDeregister" class="label label-danger" style="border-radius:4px;position:absolute;right:20px;top:51px;padding:3px 6px 4px;cursor:pointer;">Unlink Digi-ID</span>
</div>

<div class="digiUnSet alert alert-update-banner alert-update-banner-danger{if $digiidAddress} hidden{/if}">
    <div class="alert-icon" style="margin-left:10px;">
        <i class="fa fa-exclamation"></i>
    </div>
    <div class="alert-msg">
        <strong>Oh No!</strong><br>
        No Digi-ID wallet is linked to your account, register with the QR code below.
    </div>
</div>



<div class="marketing-email-optin">
    <h4>Digi-ID Registration</h4>
    <p>Link your account to your Digi-ID for fast and secure login to {$companyname}.</p>
    <div align="center" id="DigiIDRegister">
        <a href='{$DigiIDURL}' id='digiidlink'>
            <img alt='digiid login' title='DigiID Login' />
        </a>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
<script type="text/javascript" src="modules/addons/digiid/js.js"></script>