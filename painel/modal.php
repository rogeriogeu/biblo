<?php
// Modais reutilizáveis: mensagem (sucesso/erro/aviso) e confirmação de formulários.

function modal_esc($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Renderiza as modais e o script. Requer jQuery e Bootstrap 4 já carregados.
// $flash = [tipo, mensagem] (tipo: success|danger|warning|info) ou null.
function modal_render(?array $flash = null): string
{
    $mapa = [
        'success' => ['Sucesso', 'fa-check-circle', 'bg-success'],
        'danger' => ['Erro', 'fa-times-circle', 'bg-danger'],
        'warning' => ['Atenção', 'fa-exclamation-triangle', 'bg-warning'],
        'info' => ['Informação', 'fa-info-circle', 'bg-info'],
    ];
    $html = '<div class="modal fade" id="modal-mensagem" tabindex="-1" role="dialog" aria-hidden="true">'
        . '<div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">'
        . '<div class="modal-header text-white" id="modal-mensagem-topo"><h5 class="modal-title"><i class="fa" id="modal-mensagem-icone"></i> <span id="modal-mensagem-titulo"></span></h5>'
        . '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button></div>'
        . '<div class="modal-body" id="modal-mensagem-texto" style="white-space:pre-line"></div>'
        . '<div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button></div>'
        . '</div></div></div>'
        . '<div class="modal fade" id="modal-confirmar" tabindex="-1" role="dialog" aria-hidden="true">'
        . '<div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">'
        . '<div class="modal-header bg-warning"><h5 class="modal-title"><i class="fa fa-question-circle"></i> Confirmação</h5>'
        . '<button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button></div>'
        . '<div class="modal-body" id="modal-confirmar-texto" style="white-space:pre-line"></div>'
        . '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>'
        . '<button type="button" class="btn btn-primary" id="modal-confirmar-ok">Confirmar</button></div>'
        . '</div></div></div>';

    $js = '(function(){var $=window.jQuery;if(!$){return;}var tipos=' . json_encode($mapa, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) . ';'
        . 'window.mostrarModal=function(tipo,msg){var t=tipos[tipo]||tipos.info;$("#modal-mensagem-topo").attr("class","modal-header text-white "+t[2]);'
        . '$("#modal-mensagem-icone").attr("class","fa "+t[1]);$("#modal-mensagem-titulo").text(t[0]);$("#modal-mensagem-texto").text(msg);$("#modal-mensagem").modal("show");};'
        . 'var cb=null;window.confirmarModal=function(msg,aoConfirmar,aoCancelar){cb=aoConfirmar;$("#modal-confirmar-texto").text(msg);$("#modal-confirmar").data("ok",false).modal("show");'
        . '$("#modal-confirmar").off("hidden.bs.modal").on("hidden.bs.modal",function(){if(!$(this).data("ok")&&aoCancelar){aoCancelar();}});};'
        . '$("#modal-confirmar-ok").on("click",function(){$("#modal-confirmar").data("ok",true).modal("hide");if(cb){var f=cb;cb=null;f();}});'
        . '$(document).on("submit","form[data-confirm]",function(e){var f=this;if(f.dataset.confirmado==="1"){return;}e.preventDefault();'
        . 'var sub=e.originalEvent&&e.originalEvent.submitter;window.confirmarModal(f.getAttribute("data-confirm"),function(){f.dataset.confirmado="1";if(sub&&sub.name){$(f).append($("<input type=hidden>").attr("name",sub.name).val(sub.value));}f.submit();});});';
    if ($flash) {
        $js .= '$(function(){window.mostrarModal(' . json_encode((string) $flash[0]) . ',' . json_encode((string) $flash[1], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ');});';
    }
    return $html . '<script>' . $js . '})();</script>';
}
