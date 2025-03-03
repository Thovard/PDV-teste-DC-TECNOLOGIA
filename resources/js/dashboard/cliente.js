import applyMask from '../masks.js';
import $ from 'jquery';

$(function(){
    $("input[name='cpf']").each(function(){
        applyMask(this, 'cpf');
    });
    $("input[name='telefone']").each(function(){
        applyMask(this, 'telefone');
    });
    
    $('.modal').on('shown.bs.modal', function () {
        $(this).find("input[name='cpf']").each(function(){
            applyMask(this, 'cpf');
            $(this).trigger('input');
        });
        $(this).find("input[name='telefone']").each(function(){
            applyMask(this, 'telefone');
            $(this).trigger('input');
        });
    });
    $("td[data-mask='cpf']").each(function(){
        let raw = $(this).text().replace(/\D/g, '');
        let formatted = raw;
        if(raw.length > 9){
            formatted = raw.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4');
        } else if(raw.length > 6){
            formatted = raw.replace(/(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
        } else if(raw.length > 3){
            formatted = raw.replace(/(\d{3})(\d{0,3})/, '$1.$2');
        }
        $(this).text(formatted);
   });

   $("td[data-mask='telefone']").each(function(){
        let raw = $(this).text().replace(/\D/g, '');
        let formatted = raw;
        if(raw.length > 10){
            formatted = raw.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
        } else {
            formatted = raw.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
        }
        $(this).text(formatted);
   });
});
