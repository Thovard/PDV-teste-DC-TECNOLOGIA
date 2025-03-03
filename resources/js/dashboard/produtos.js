import applyMask from '../masks.js';
import $ from 'jquery';

$(function(){
    $("input[name='preco']").each(function(){
         applyMask(this, 'dinheiro');
    });
    $('.modal').on('shown.bs.modal', function () {
         $(this).find("input[name='preco']").each(function(){
             applyMask(this, 'dinheiro');
             $(this).trigger('input');
         });
    });
    $("td[data-mask='dinheiro']").each(function(){
         let raw = $(this).text().replace(/\D/g, '');
         let formatted = '';
         if(raw === '') {
             formatted = '';
         } else {
             if (raw.length < 3) {
                 raw = raw.padStart(3, '0');
             }
             let intPart = raw.slice(0, -2);
             let decimalPart = raw.slice(-2);
             intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
             formatted = 'R$ ' + intPart + ',' + decimalPart;
         }
         $(this).text(formatted);
    });
});
