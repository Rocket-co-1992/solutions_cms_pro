/*!
 * jQuery PageFlow - JavaScript Library for Drag-and-Drop Page Building
 * Author: Xavier, PANDAO, FR
 * License: Envato Regular/Extended License
 * License URL: https://codecanyon.net/licenses/standard

 * Description:
 * jQuery PageFlow is a flexible, intuitive page builder designed for creating responsive page layouts. 
 * Features include customizable grid layouts, dynamic CKEditor integration, and content management tools.
 * This file contains core JavaScript functions, including drag-and-drop handling, content structure management, 
 * and export functionalities to streamline page construction and maintenance.
 *
 * Version: 1.0.0
 *
 * Copyright © 2024 PANDAO. All rights reserved.
 *
 * This file is licensed to the original purchaser only. Redistribution or resale
 * of this file or its contents, either in part or whole, is prohibited unless
 * permitted by the license agreement.
 *
 * Created on: October, 2024
 *
 * For support: support@pandao.eu
 */

import { editorConfig } from './ckeditor-config.js';
import { BalloonEditor } from 'ckeditor5Balloon';

async function pflow_generateToken(uniqid, timestamp) {
    const msg = 'sessid_' + uniqid + timestamp;
    const msgBuffer = new TextEncoder().encode(msg);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
}

const pflow_time = Math.round(Date.now() / 1000);
const pflow_uniqId = crypto.randomUUID();
const pflow_token = await pflow_generateToken(pflow_uniqId, pflow_time);

(function ($) {
    $.fn.pageflow = function (options) {
        
        const settings = $.extend({
            uploadUrl: './handlers/pflow_media_uploader.php',
            removeUrl: './handlers/pflow_media_remover.php',
            showExportBtn: true,
            exportToInput: false,
            inputId: $(this).attr('rel'),
            editorConfig: editorConfig
        }, options);
        
        return this.each(function () {

            const $container = $(this);

            $(`<div class="pflow-elmt-list">
                <div class="pflow-drag-elmt" data-type="col-12">
                    <svg viewBox="0 0 24 24">
                        <path d="M2 20h20V4H2v16Zm-1 0V4a1 1 0 0 1 1-1h20a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1Z"></path>
                    </svg>
                    <span>1 Column</span>
                </div>
                <div class="pflow-drag-elmt" data-type="col-6">
                    <svg viewBox="0 0 24 24">
                        <path d="M2 20h8V4H2v16Zm-1 0V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1ZM13 20h8V4h-8v16Zm-1 0V4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-8a1 1 0 0 1-1-1Z"></path>
                    </svg>
                    <span>2 Cols</span>
                </div>
                <div class="pflow-drag-elmt" data-type="col-8-4">
                    <svg viewBox="0 0 24 24">
                        <path d="M2 20h5V4H2v16Zm-1 0V4a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1ZM10 20h12V4H10v16Zm-1 0V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H10a1 1 0 0 1-1-1Z"></path>
                    </svg>
                    <span>2 Cols 7/3</span>
                </div>
                <div class="pflow-drag-elmt" data-type="col-4-8">
                    <svg viewBox="0 0 24 24">
                        <path d="M17 20h5V4h-5v16Zm-1 0V4a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1ZM2 20h12V4H2v16Zm-1 0V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1Z"></path>
                    </svg>
                    <span>2 Cols 3/7</span>
                </div>
                <div class="pflow-drag-elmt" data-type="col-4">
                    <svg viewBox="0 0 24 24">
                        <path d="M2 20h4V4H2v16Zm-1 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1ZM17 20h4V4h-4v16Zm-1 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1ZM9.5 20h4V4h-4v16Zm-1 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z"></path>
                    </svg>
                    <span>3 Cols</span>
                </div>
                <div class="pflow-drag-elmt" data-type="col-3">
                    <svg viewBox="0 0 24 24">
                        <path d="M2 20h3V4H2v16Zm-1 0V4a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1ZM8 20h3V4H8v16Zm-1 0V4a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1ZM14 20h3V4h-3v16Zm-1 0V4a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-3a1 1 0 0 1-1-1ZM20 20h3V4h-3v16Zm-1 0V4a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-3a1 1 0 0 1-1-1Z"></path>
                    </svg>
                    <span>4 Cols</span>
                </div>
                <div class="pflow-drag-elmt" data-type="hr">
                    <svg viewBox="0 0 640 512">
                        <path d="M0 256c0-8.8 7.2-16 16-16l608 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L16 272c-8.8 0-16-7.2-16-16z"/></svg>
                    </svg>
                    <span>Separator</span>
                </div>
                <div class="pflow-drag-elmt" data-type="h1">
                    <svg viewBox="0 0 448 512">
                        <path d="M0 48c0-8.8 7.2-16 16-16l64 0 64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L96 64l0 160 256 0 0-160-48 0c-8.8 0-16-7.2-16-16s7.2-16 16-16l64 0 64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-48 0 0 176 0 208 48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-128 0c-8.8 0-16-7.2-16-16s7.2-16 16-16l48 0 0-192L96 256l0 192 48 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L16 480c-8.8 0-16-7.2-16-16s7.2-16 16-16l48 0 0-208L64 64 16 64C7.2 64 0 56.8 0 48z"/>
                    </svg>
                    <span>Heading</span>
                </div>
                <div class="pflow-drag-elmt" data-type="ckeditor">
                    <svg viewBox="0 0 24 24">
                        <path d="M21,6V8H3V6H21M3,18H12V16H3V18M3,13H21V11H3V13Z"></path>
                    </svg>
                    <span>Text</span>
                </div>
                <div class="pflow-drag-elmt" data-type="img">
                    <svg viewBox="0 0 512 512">
                        <path d="M64 64C46.3 64 32 78.3 32 96l0 233.4 67.7-67.7c15.6-15.6 40.9-15.6 56.6 0L224 329.4 355.7 197.7c15.6-15.6 40.9-15.6 56.6 0L480 265.4 480 96c0-17.7-14.3-32-32-32L64 64zM32 374.6L32 416c0 17.7 14.3 32 32 32l41.4 0 96-96-67.7-67.7c-3.1-3.1-8.2-3.1-11.3 0L32 374.6zM389.7 220.3c-3.1-3.1-8.2-3.1-11.3 0L150.6 448 448 448c17.7 0 32-14.3 32-32l0-105.4-90.3-90.3zM0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zm160 48a16 16 0 1 0 -32 0 16 16 0 1 0 32 0zm-64 0a48 48 0 1 1 96 0 48 48 0 1 1 -96 0z"/>
                    </svg>
                    <span>Image</span>
                </div>
                <div class="pflow-drag-elmt" data-type="card">
                    <svg viewBox="0 0 552 552">
                        <path d="M160 64c-17.7 0-32 14.3-32 32l0 320c0 11.7-3.1 22.6-8.6 32L432 448c26.5 0 48-21.5 48-48l0-304c0-17.7-14.3-32-32-32L160 64zM64 480c-35.3 0-64-28.7-64-64L0 160c0-35.3 28.7-64 64-64l0 32c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32s32-14.3 32-32L96 96c0-35.3 28.7-64 64-64l288 0c35.3 0 64 28.7 64 64l0 304c0 44.2-35.8 80-80 80L64 480zM384 112c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16zM160 304c0-8.8 7.2-16 16-16l256 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-256 0c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16l256 0c8.8 0 16 7.2 16 16s-7.2 16-16 16l-256 0c-8.8 0-16-7.2-16-16zm32-144l128 0 0-96-128 0 0 96zM160 120c0-13.3 10.7-24 24-24l144 0c13.3 0 24 10.7 24 24l0 112c0 13.3-10.7 24-24 24l-144 0c-13.3 0-24-10.7-24-24l0-112z"/>
                    </svg>
                    <span>Article</span>
                </div>
                <div class="pflow-drag-elmt" data-type="video">
                    <svg viewBox="0 0 24 24">
                        <path d="M10,15L15.19,12L10,9V15M21.56,7.17C21.69,7.64 21.78,8.27 21.84,9.07C21.91,9.87 21.94,10.56 21.94,11.16L22,12C22,14.19 21.84,15.8 21.56,16.83C21.31,17.73 20.73,18.31 19.83,18.56C19.36,18.69 18.5,18.78 17.18,18.84C15.88,18.91 14.69,18.94 13.59,18.94L12,19C7.81,19 5.2,18.84 4.17,18.56C3.27,18.31 2.69,17.73 2.44,16.83C2.31,16.36 2.22,15.73 2.16,14.93C2.09,14.13 2.06,13.44 2.06,12.84L2,12C2,9.81 2.16,8.2 2.44,7.17C2.69,6.27 3.27,5.69 4.17,5.44C4.64,5.31 5.5,5.22 6.82,5.16C8.12,5.09 9.31,5.06 10.41,5.06L12,5C16.19,5 18.8,5.16 19.83,5.44C20.73,5.69 21.31,6.27 21.56,7.17Z"></path>
                    </svg>
                    <span>Video</span>
                </div>
                <div class="pflow-drag-elmt" data-type="button">
                    <svg viewBox="0 0 24 24">
                        <path d="M22 9c0-.6-.5-1-1.3-1H3.4C2.5 8 2 8.4 2 9v6c0 .6.5 1 1.3 1h17.4c.8 0 1.3-.4 1.3-1V9zm-1 6H3V9h18v6z"></path><path d="M4 11.5h16v1H4z"></path>
                    </svg>
                    <span>Button</span>
                </div>
                <button class="btn btn-primary pflow-export">Export Layout</button>
            </div>
            <div class="pflow-canvas"></div>
            <a class="pflow-credits" href="https://pageflow.pandao.eu">&copy Powered by <strong>PageFlow</strong> by Pandao</a>`).appendTo($container);

            const $modal = $(`<div class="pflow-modal" style="display:none;">
                <div class="pflow-modal-content">
                    <span class="pflow-close">&times;</span>
                    <pre class="pflow-exportCode"></pre>
                    <span class="pflow-copy" data-bs-toggle="tooltip">
                        <svg width="24" height="24" viewBox="0 0 24 24">
                            <path d="M7 5C7 3.34315 8.34315 2 10 2H19C20.6569 2 22 3.34315 22 5V14C22 15.6569 20.6569 17 19 17H17V19C17 20.6569 15.6569 22 14 22H5C3.34315 22 2 20.6569 2 19V10C2 8.34315 3.34315 7 5 7H7V5ZM9 7H14C15.6569 7 17 8.34315 17 10V15H19C19.5523 15 20 14.5523 20 14V5C20 4.44772 19.5523 4 19 4H10C9.44772 4 9 4.44772 9 5V7ZM5 9C4.44772 9 4 9.44772 4 10V19C4 19.5523 4.44772 20 5 20H14C14.5523 20 15 19.5523 15 19V10C15 9.44772 14.5523 9 14 9H5Z" fill="currentColor"></path>
                        </svg>
                        Copy Code
                    </span>
                </div>
            </div>`).appendTo($container);

            let idCounter = 0;

            const elementsConfig = {
                'col-12': `<div data-dpz="horizontal" class="row" id="row-${++idCounter}"><div data-dpz="vertical" class="col col-12" id="col-${idCounter}-1"></div></div>`,
                'col-6': `<div data-dpz="horizontal" class="row" id="row-${++idCounter}"><div data-dpz="vertical" class="col col-6" id="col-${idCounter}-1"></div><div data-dpz="vertical" class="col col-6" id="col-${idCounter}-2"></div></div>`,
                'col-8-4': `<div data-dpz="horizontal" class="row" id="row-${++idCounter}"><div data-dpz="vertical" class="col col-8" id="col-${idCounter}-1"></div><div data-dpz="vertical" class="col col-4" id="col-${idCounter}-2"></div></div>`,
                'col-4-8': `<div data-dpz="horizontal" class="row" id="row-${++idCounter}"><div data-dpz="vertical" class="col col-4" id="col-${idCounter}-1"></div><div data-dpz="vertical" class="col col-8" id="col-${idCounter}-2"></div></div>`,
                'col-4': `<div data-dpz="horizontal" class="row" id="row-${++idCounter}"><div data-dpz="vertical" class="col col-4" id="col-${idCounter}-1"></div><div data-dpz="vertical" class="col col-4" id="col-${idCounter}-2"></div><div data-dpz="vertical" class="col col-4" id="col-${idCounter}-3"></div></div>`,
                'col-3': `<div data-dpz="horizontal" class="row" id="row-${++idCounter}"><div data-dpz="vertical" class="col col-3" id="col-${idCounter}-1"></div><div data-dpz="vertical" class="col col-3" id="col-${idCounter}-2"></div><div data-dpz="vertical" class="col col-3" id="col-${idCounter}-3"></div><div data-dpz="vertical" class="col col-3" id="col-${idCounter}-4"></div></div>`,
                'hr': `<div data-dpz="horizontal" class="pflow-hr"><hr></div>`,
                'h1': `<h1 data-dpz="horizontal" class="pflow-heading" contenteditable="true" placeholder="">Your title here...</h1>`,
                'ckeditor': `<div data-dpz="horizontal" class="pflow-editor"><div class="ckeditor"></div></div>`,
                'img': `<figure data-dpz="horizontal" class="figure image pflow-img"><img class="figure-img img-fluid rounded" src="" alt=""></figure>`,
                'video': `<figure data-dpz="horizontal" class="figure pflow-video"><video controls><source src="" type="video/mp4"></video></figure>`,
                'text': `<p data-dpz="horizontal" class="pflow-text" contenteditable="true" placeholder="">Your text here...</p>`,
                'button': `<div data-dpz="horizontal" class="pflow-button"><a href="" class="btn btn-primary" contenteditable="true" placeholder="">Your text here...</a></div>`,
                'card': `<div data-dpz="horizontal" class="pflow-card"><figure class="figure image pflow-img"><img class="figure-img img-fluid rounded" src="" alt=""></figure><h3 contenteditable="true">Title here</h3><p contenteditable="true">Lorem ipsum dolor sit amet, consectetur adipiscing elit</p></div>`
            };
            
            const $closeModal = $('.pflow-close', $modal);
            const $exportCode = $('.pflow-exportCode', $modal);
            const $canvas = $('.pflow-canvas', $container);
            const $dragElmts = $('.pflow-drag-elmt', $container);
            const $exportBtn = $('.pflow-export', $container);

            let draggedElement = null;
            let isDragging = false;
            let touchTimeout = null;
            let dropZoneCounter = 0;
            let initialPosition = { left: 0, top: 0 }; // Store the initial position of the dragged element
            let zoneType = '';

            // Add initial drop zone to the canvas
            $canvas.append('<div class="pflow-drop-zone" id="dropzone-' + (++dropZoneCounter) + '"></div>');

            if(settings.exportToInput) importLayoutToContainer();

            $(window).on('resize', function () {
                updateDropZones();
            });

            $canvas.on('click', '[data-dpz]', function (e) {
                e.stopPropagation();
                selectTarget($(this));
            });

            function selectTarget($target) {
                if(!$target.hasClass('pflow-selected')) {
                    $('[data-dpz]', $canvas).removeClass('pflow-selected');
                    $target.addClass('pflow-selected');
                    addToolbar($target[0]);
                }
            }

            $canvas.on('click', function (e) {
                e.stopPropagation();
                $('[data-dpz]', $canvas).removeClass('pflow-selected');
                $('.pflow-toolbar', $canvas).remove();
            });

            $canvas.on('mouseover', '[data-dpz]', function (e) {
                e.stopPropagation();
                $('[data-dpz]', $canvas).removeClass('pflow-over');
                $(this).addClass('pflow-over');
            });
            $canvas.on('mouseleave', '[data-dpz]', function (e) {
                e.stopPropagation();
                $(this).removeClass('pflow-over');
            });
            $canvas.on('mouseleave', '.row', function (e) {
                $('.pflow-vertical-drop-zone').removeClass('active').css('opacity', '0');
                e.stopPropagation();
                $(this).removeClass('pflow-hovered');
            });

            // Start dragging event
            $dragElmts.on('mousedown touchstart', function (e) {
                e.preventDefault();

                if (e.type === 'touchstart') {
                    touchTimeout = setTimeout(() => {
                        startDrag($(this));
                    }, 200);
                } else {
                    startDrag($(this));
                }
                $(document).on("mouseup touchend", function() {
                    stopDrag(false);
                });
            });

            // Close modal
            $closeModal.on('click', function () {
                $modal.hide();
            });
            $(window).on('click', function (event) {
                if (event.target === $modal[0]) {
                    $modal.hide();
                }
            });
            
            $('[data-bs-toggle="tooltip"]', $container).tooltip();
        
            $('.pflow-copy', $container).on('click', function() {
                const codeBlock = formatHtml($exportCode.text());
            
                navigator.clipboard.writeText(codeBlock).then(() => {
                    const $btn = $(this);
            
                    if (!$btn.data('bs.tooltip')) {
                        $btn.tooltip({
                            trigger: 'manual',
                            placement: 'top'
                        });
                    }
            
                    $btn.attr('data-bs-original-title', 'Copied!').tooltip('show');
            
                    setTimeout(() => {
                        $btn.tooltip('hide').attr('data-bs-original-title', '');
                    }, 1000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            });

            
            if(settings.showExportBtn !== true) {
                $exportBtn.hide();
            } else {
                $exportBtn.on('click', function () {
                    let layoutCode = exportLayout();
                    showModal(layoutCode);
                });
            }

            $canvas.on('dblclick ', '.pflow-img img', function () {

                let imgObj = $(this);
                let fileInput = $('<input/>').attr({
                    name: 'pflow_fileinput',
                    type: 'file',
                    accept: 'image/*',
                    style: 'display:none'
                });

                $container.append(fileInput);
                fileInput.click();

                fileInput.on('change', function (event) {
                    let file = event.target.files[0];

                    if (file) {
                        const data = new FormData();
                        data.append('upload', file);
                        data.append('uniqid', pflow_uniqId);
                        data.append('timestamp', pflow_time);
                        data.append('token', pflow_token);

                        $.ajax({
                            url: settings.uploadUrl,
                            type: 'POST',
                            data: data,
                            contentType: false,
                            processData: false,
                            success: function (response) {
                                imgObj.attr('src', response.url);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                console.error('Upload error:', textStatus, errorThrown);
                            }
                        });
                    }
                    fileInput.remove();
                });
            });

            const exportObserver = new MutationObserver(debounce(() => {
                exportToInput();
            }, 500));
            
            exportObserver.observe($canvas[0], {
                childList: true,
                subtree: true,
                characterData: true,
                attributes: true,
            });

            function importLayoutToContainer() {
                const $textarea = $(settings.inputId);
                const html = $textarea.val();
                const $content = $('<div>' + html + '</div>');
            
                const GROUPABLE_ELEMENTS = ['p', 'blockquote', 'ul', 'ol'];
            
                $content.find('h1, h2, h3, h4, h5, h6').each(function() {
                    $(this).attr({
                        'data-dpz': 'horizontal'
                    });
                });
                $content.find('h1, h2, h3, h4, h5, h6, p').each(function() {
                    $(this).attr({
                        'contenteditable': 'true'
                    });
                });
            
                let paragraphs = [];
                $content.children().each(function() {
                    const $elem = $(this);
                    
                    if (GROUPABLE_ELEMENTS.includes($elem.prop('tagName').toLowerCase())) {
                        paragraphs.push($elem);
                    } else {
                        if (paragraphs.length) {
                            wrapParagraphsInCKEditor(paragraphs);
                            paragraphs = [];
                        }
                    }
                });
            
                if (paragraphs.length) wrapParagraphsInCKEditor(paragraphs);
            
                wrapInFigure($content, 'img', 'pflow-img');
                wrapInFigure($content, 'video', 'pflow-video');
            
                $content.find('.row').each(function() {
                    const $row = $(this).attr({
                        'data-dpz': 'horizontal',
                        'class': 'row'
                    });
                    $row.find('.col').each(function() {
                        const $col = $(this);
                        const colClass = $col.attr('class').match(/col-(\d+)/);
                        if (colClass) {
                            $col.addClass(`col-${colClass[1]}`).attr('data-dpz', 'vertical');
                        }
                    });
                });
            
                $content.find('hr').each(function() {
                    $(this).wrap('<div data-dpz="horizontal" class="pflow-hr"></div>');
                });
            
                $content.find('.pflow-card').each(function() {
                    const $card = $(this).attr('data-dpz', 'horizontal');
                    const $img = $card.find('img');
                    if ($img.length) {
                        $img.addClass('figure-img img-fluid rounded').attr('src', $img.attr('src'));
                    }
                    $card.find('h3').removeAttr('data-dpz');
                });

                $content.find('.pflow-button').each(function() {
                    const $button = $(this).attr('data-dpz', 'horizontal');
                    $button.find('a').attr('contenteditable', 'true');
                });
            
                $canvas.html($content.html());
            
                $canvas.find('.ckeditor').each(function() {
                    initCKEditor(this);
                });
                setTimeout(function() {
                    $canvas.find('.ck-link_selected').removeClass('ck-link_selected');
                }, 0);
                
                updateDropZones();
            }            
            
            function wrapInFigure($content, tagName, figureClass) {
                $content.find(tagName).not('.pflow-card ' + tagName).each(function() {
                    const $element = $(this);
                    if ($element.parent().is('figure')) {
                        $element.parent().addClass(figureClass + ' figure').attr('data-dpz', 'horizontal');
                    } else {
                        $element.wrap(`<figure data-dpz="horizontal" class="figure ${figureClass}"></figure>`);
                    }
                });
            }
            
            function wrapParagraphsInCKEditor(paragraphs) {
                const editorContainer = $('<div data-dpz="horizontal" class="pflow-editor"><div class="ckeditor"></div></div>');
                paragraphs[0].before(editorContainer);
                paragraphs.forEach(p => editorContainer.find('.ckeditor').append(p));
            }            

            function initCKEditor(elm) {
                BalloonEditor.create(elm, settings.editorConfig)
                .then(editor => {
                    editor.editing.view.focus();
                    elm.ckeditorInstance = editor;
                })
                .catch(function (error) {
                    console.log(elm);
                    console.error("CKEditor init error:", error);
                });
            }

            // Show modal
            function showModal(content) {
                $modal.show();
                let encodedStr = encodeHTMLEntities(content);
                $exportCode.html(encodedStr);
            }

            function encodeHTMLEntities(text) {
                var textArea = document.createElement('textarea');
                textArea.innerText = text;
                return textArea.innerHTML;
            }

            function formatHtml(html) {
                let formatted = '';
                let pad = 0;
                const indent = '    ';
            
                const selfClosingTags = ['img', 'hr', 'br', 'input', 'meta', 'link', 'area', 'base', 'col', 'embed', 'source', 'track', 'wbr'];
            
                html.split(/(<[^>]+>)/).forEach(function (element) {
                    element = element.trim();
            
                    if (element === '') return;
            
                    if (element.match(/^<\/\w/)) {
                        pad -= 1;
                        formatted += indent.repeat(pad) + element + '\n';
                    } else if (element.match(/^<(\w+)[^>]*\/?>/) && (selfClosingTags.includes(RegExp.$1) || element.endsWith('/>'))) {
                        formatted += indent.repeat(pad) + element + '\n';
                    } else if (element.match(/^<\w/)) {
                        formatted += indent.repeat(pad) + element + '\n';
                        pad += 1;
                    } 
                    else {
                        formatted += indent.repeat(pad) + element + '\n';
                    }
                });
            
                return formatted.trim();
            }

            function exportLayout() {
                let clonedCanvas = $canvas.clone();

                clonedCanvas.find('.pflow-drop-zone').remove();
                clonedCanvas.find('.pflow-vertical-drop-zone').remove();
                clonedCanvas.find('.pflow-toolbar').remove();

                clonedCanvas.find('.pflow-selected').removeClass('pflow-selected');
                clonedCanvas.find('.pflow-over').removeClass('pflow-over');

                if(!settings.exportToInput) {
                    clonedCanvas.find('.pflow-heading').removeClass('pflow-heading');
                    clonedCanvas.find('.pflow-card').removeClass('pflow-card');
                    clonedCanvas.find('.pflow-text').removeClass('pflow-text');
                    clonedCanvas.find('.pflow-img').removeClass('pflow-img');
                    clonedCanvas.find('.pflow-video').removeClass('pflow-video');
                    clonedCanvas.find('.pflow-button').removeClass('pflow-button');
                    clonedCanvas.find('.pflow-text div').each(function() {
                        let content = $(this).html();
                        $(this).replaceWith('<br>' + content);
                    });
                }

                clonedCanvas.find('.pflow-hr').replaceWith(function() {
                    return $(this).contents();
                });

                let editorMap = [];
                $('.ckeditor', $container).each(function (index) {
                    let editorElement = $(this).get(0);
                    if (editorElement.ckeditorInstance) {
                        let editorInstance = editorElement.ckeditorInstance;
                        let editorData = editorInstance.getData();
                        
                        let clonedEditorParent = clonedCanvas.find('.pflow-editor').eq(index);
                        editorMap.push({ editorParent: clonedEditorParent, content: editorData.trim() });
                    }
                });

                editorMap.forEach(function (item) {
                    item.editorParent.replaceWith(item.content);
                });      

                clonedCanvas.find('[contenteditable]').removeAttr('contenteditable');    
                clonedCanvas.find('[class=""]').removeAttr('class');
                clonedCanvas.find('[id]').removeAttr('id');
                clonedCanvas.find('[data-dpz]').removeAttr('data-dpz');
                clonedCanvas.find('[placeholder]').removeAttr('placeholder');  

                let layoutCode = clonedCanvas.html();
                layoutCode = formatHtml(layoutCode);
                return layoutCode;
            }

            function startDrag(element, move = false) {
                isDragging = true;
                let isMove = move;
                if(!isMove) {
                    draggedElement = element.clone();
                    draggedElement.addClass('pflow-dragging').hide();
                    $container.append(draggedElement);
                    
                    $('[data-dpz]', $canvas).removeClass('pflow-selected');
                    $('.pflow-toolbar', $canvas).remove();
                    
                } else {
                    draggedElement = element;
                }
                $(document).on('mousemove touchmove', function (e) {
                    moveDraggedElement(isMove, e);
                });
            }
            
            // Function to activate a drop zone
            function activateDropZone(dropZone) {
                $('*', $canvas).removeClass('pflow-hovered');
                $('.pflow-drop-zone', $canvas).removeClass('active').css('opacity', '0');
                
                if (dropZone !== null) {
                    dropZone.addClass('active').css('opacity', '1'); // Activate the targeted drop zone
                    dropZone.parent().not('.pflow-canvas').addClass('pflow-hovered');
                }
            }

            function moveDraggedElement(move, e) {
                if (!isDragging || !draggedElement) return;
            
                if (touchTimeout) {
                    clearTimeout(touchTimeout);
                }

                let isMove = move;

                const clientX = e.clientX || e.originalEvent.touches[0].clientX;
                const clientY = e.clientY || e.originalEvent.touches[0].clientY;

                const containerOffset = $container.offset();

                const scrollLeft = $(document).scrollLeft();
                const scrollTop = $(document).scrollTop();
            
                // Move the element with the cursor
                if(!isMove) {
                    draggedElement.css({
                        display: 'block',
                        position: 'absolute',
                        left: clientX - containerOffset.left + scrollLeft - draggedElement.width() / 2,
                        top: clientY - containerOffset.top + scrollTop - draggedElement.height() / 2,
                    });
                    draggedElement.hide(); // Temporarily hide to detect the drop zone
                }
                let hoveredElement = document.elementFromPoint(clientX, clientY); // Get the exact element under the cursor
                draggedElement.show();
                
                zoneType = draggedElement.hasClass('col') ? 'vertical' : 'horizontal';
                
                if (zoneType == 'horizontal') {
                    
                    // Hovering over a drop zone
                    if ($(hoveredElement).hasClass('pflow-drop-zone')) {
                        activateDropZone($(hoveredElement));
                        return;
                    }

                    // Hovering over a column (col)
                    if ($(hoveredElement).hasClass('col')) {
                        // Find the closest drop zone in the column
                        let dropZoneInCol = findDropZoneInCol($(hoveredElement), clientY);
                        if (dropZoneInCol.length > 0) {
                            activateDropZone(dropZoneInCol);
                        }
                    } else if ($(hoveredElement).hasClass('row')) {
                        // If hovering over a row
                        let rowElement = $(hoveredElement).closest('.row');
                        let rowTop = rowElement.offset().top;
                        let rowHeight = rowElement.outerHeight();
                        let rowMidPoint = rowTop + rowHeight / 2;
                
                        // Activate the drop zone closest to the cursor
                        if (clientY < rowMidPoint) {
                            let dropZoneBefore = rowElement.prev('.pflow-drop-zone');
                            if (dropZoneBefore.length) {
                                activateDropZone(dropZoneBefore);
                            }
                        } else {
                            let dropZoneAfter = rowElement.next('.pflow-drop-zone');
                            if (dropZoneAfter.length) {
                                activateDropZone(dropZoneAfter);
                            }
                        }
                    } else if ($(hoveredElement).hasClass('pflow-canvas')) {
                        // If hovering over the canvas but no specific col or drop zone
                        let lastDropZone = findCanvasDropZone(clientY);
                        if (lastDropZone) {
                            activateDropZone($(lastDropZone));
                        }
                    } else {
                        activateDropZone(null);
                    }
                }
                if (zoneType == 'vertical') {
                    
                    if ($(hoveredElement).hasClass('pflow-vertical-drop-zone')) {
                        activateDropZone($(hoveredElement));
                    } else {
                        let row = $(hoveredElement).parent('.row'); // Find the parent row of the column
                        
                        $('.pflow-vertical-drop-zone', $canvas).removeClass('active').css('opacity', '0');

                        if (row.length > 0 && isCursorInsideRow(row, clientX, clientY)) {
                            let closestDropZone = findVerticalDropZone(row, clientX);
                            // Activate the closest drop zone
                            if (closestDropZone) {
                                activateDropZone(closestDropZone);
                            }
                        }
                    }
                }
            }

            function isCursorInsideRow(row, cursorX, cursorY) {
                const rowOffset = row.offset();
                const scrollLeft = $(document).scrollLeft();
                const scrollTop = $(document).scrollTop();
            
                const rowViewportLeft = rowOffset.left - scrollLeft;
                const rowViewportTop = rowOffset.top - scrollTop;
                
                const rowWidth = row.outerWidth();
                const rowHeight = row.outerHeight();
            
                return (cursorX >= rowViewportLeft && cursorX <= rowViewportLeft + rowWidth &&
                        cursorY >= rowViewportTop && cursorY <= rowViewportTop + rowHeight);
            }
            
            function findClosestDropZone(container, selector, cursorPos, axis = 'Y') {
                let selectedDropZone = null;
                let closestDistance = Infinity;
            
                const containerOffset = $canvas.offset();
                const containerScrollOffset = axis === 'Y' ? $(document).scrollTop() : $(document).scrollLeft();
                const containerViewportPos = axis === 'Y'
                    ? containerOffset.top - containerScrollOffset
                    : containerOffset.left - containerScrollOffset;
            
                container.find(selector).each(function () {
                    
                    const dropZonePos = axis === 'Y' 
                        ? $(this).offset().top - containerScrollOffset
                        : $(this).offset().left - containerScrollOffset;
            
                    const distance = Math.abs(dropZonePos - cursorPos);
            
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        selectedDropZone = $(this);
                    }
                });
            
                return selectedDropZone;
            }
            
            function findCanvasDropZone(cursorY) {
                return findClosestDropZone($canvas, '> .pflow-drop-zone', cursorY, 'Y');
            }
            
            function findDropZoneInCol(col, cursorY) {
                return findClosestDropZone(col, '> .pflow-drop-zone', cursorY, 'Y');
            }

            function findVerticalDropZone(container, cursorX) {
                return findClosestDropZone(container, '> .pflow-vertical-drop-zone', cursorX, 'X');
            }

            function updateDropZones() {
                $('.pflow-drop-zone, .pflow-vertical-drop-zone', $canvas).remove();

                $canvas.find('[data-dpz]').each(function () {
                    let elmt = $(this);

                    if (elmt.hasClass('row')) {
                        let columns = elmt.find('.col');

                        // Add drop zones inside columns and before/after rows
                        columns.each(function (index) {
                            let col = $(this);
                            let colPosition = col.position();
                            let colWidth = col.outerWidth();
                            let beforeDropZone, afterDropZone;

                            // Add drop zone before the first column
                            if (index === 0) {
                                beforeDropZone = $('<div class="pflow-vertical-drop-zone left"></div>');
                                beforeDropZone.attr('id', 'dropzone-' + (++dropZoneCounter));
                                beforeDropZone.css({
                                    'left': (colPosition.left - 2) + 'px'
                                });
                                col.before(beforeDropZone);
                            }

                            // Add drop zone after each column
                            afterDropZone = $('<div class="pflow-vertical-drop-zone right"></div>');
                            afterDropZone.attr('id', 'dropzone-' + (++dropZoneCounter));
                            afterDropZone.css({
                                'left': (colPosition.left + colWidth - 2) + 'px'
                            });
                            col.after(afterDropZone); // Insert the drop zone after each column


                            // Add drop zone inside the column 
                            let insideDropZone = $('<div class="pflow-drop-zone inside-col"></div>');
                            insideDropZone.attr('id', 'dropzone-' + (++dropZoneCounter));
                            col.append(insideDropZone);
                        });
                    }
                    if (elmt.is('[data-dpz="horizontal"]')) {
                        let beforeDropZone = $('<div class="pflow-drop-zone"></div>');
                        beforeDropZone.attr('id', 'dropzone-' + (++dropZoneCounter));
                        elmt.before(beforeDropZone);

                        let afterDropZone = $('<div class="pflow-drop-zone"></div>');
                        afterDropZone.attr('id', 'dropzone-' + (++dropZoneCounter));
                        elmt.after(afterDropZone);
                    }
                });

                let finalDropZone = $('<div class="pflow-drop-zone"></div>');
                finalDropZone.attr('id', 'dropzone-' + (++dropZoneCounter));
                $canvas.append(finalDropZone);

                // Remove duplicate drop zones
                $('.pflow-drop-zone', $canvas).each(function () {
                    if ($(this).next().hasClass('pflow-drop-zone')) {
                        $(this).next().remove();
                    }
                });
            }

            // Handle stop dragging and insertion into the canvas
            function stopDrag(isMove = false) {

                let newElement;

                $('.pflow-canvas *', $container).removeClass('pflow-hovered');

                if (zoneType == 'horizontal') {
                    var activeDropZone = $('.pflow-drop-zone.active', $canvas);
                }else if (zoneType == 'vertical') {
                    var activeDropZone = $('.pflow-vertical-drop-zone.active', $canvas);
                }
                
                if (isDragging && activeDropZone.length) {

                    if(!isMove) {

                        let elementType = draggedElement.data('type');

                        if (elementsConfig[elementType]) {
                            newElement = $(elementsConfig[elementType]);

                            activeDropZone.after(newElement);
                            draggedElement.remove();
                            draggedElement = null;
                                
                            setTimeout(function() {
                                newElement.addClass('pflow-selected');
                            }, 0);
    
                            if (elementType === 'ckeditor') initCKEditor(newElement.find('.ckeditor').get(0));
                            
                            addToolbar(newElement[0]);
                        }

                    } else {
                        if (zoneType == 'vertical') {
                            var row_from = draggedElement.parent('.row');
                            var row_target = activeDropZone.parent('.row');
                        }
                    
                        newElement = draggedElement.clone();
                        activeDropZone.after(newElement);
                        draggedElement.remove();
                        draggedElement = null;
                        
                        setTimeout(function() {
                            newElement.addClass('pflow-selected');
                        }, 0);

                        if (zoneType == 'vertical') {
                            if (row_from.not(row_target)) {
                                updateRowCols(row_from);
                                updateRowCols(row_target);
                            }
                        }
                        if (newElement.hasClass('pflow-editor')) initCKEditor(newElement.find('.ckeditor').get(0));
                        
                        addToolbar(newElement[0]);
                    }
                } else if(!isMove) {
                    
                    draggedElement.animate({
                        left: initialPosition.left,
                        top: initialPosition.top,
                        opacity: 0
                    }, 500, function () {
                        draggedElement.remove();
                        draggedElement = null;
                    });
                }

                updateDropZones();
                isDragging = false;
                $(document).off('mousemove touchmove');
                $(document).off('mouseup touchend');
                clearTimeout(touchTimeout);
                if(settings.exportToInput === true) {
                    exportToInput();
                }
            }

            function debounce(func, wait) {
                let timeout;
                return function(...args) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(this, args), wait);
                };
            }

            function exportToInput() {
                let layoutCode = exportLayout();
                if($(settings.inputId).length) {
                    $(settings.inputId).html(layoutCode);
                }
            }

            function adjustColumnWidth($pflowToolbar, $currentCol, $adjacentCol, direction, action) {

                let currentColSize = getColumnSize($currentCol);
                let adjacentColSize = getColumnSize($adjacentCol);

                if (action === 'expand') {

                    if (direction === 'left' && currentColSize < 12 && adjacentColSize > 1) {

                        $currentCol.removeClass(`col-${currentColSize}`).addClass(`col-${currentColSize + 1}`);
                        $adjacentCol.removeClass(`col-${adjacentColSize}`).addClass(`col-${adjacentColSize - 1}`);

                    } else if (direction === 'right' && currentColSize < 12 && adjacentColSize > 1) {

                        $currentCol.removeClass(`col-${currentColSize}`).addClass(`col-${currentColSize + 1}`);
                        $adjacentCol.removeClass(`col-${adjacentColSize}`).addClass(`col-${adjacentColSize - 1}`);
                    }
                } else if (action === 'collapse') {

                    if (direction === 'left' && currentColSize > 1) {

                        $currentCol.removeClass(`col-${currentColSize}`).addClass(`col-${currentColSize - 1}`);
                        $adjacentCol.removeClass(`col-${adjacentColSize}`).addClass(`col-${adjacentColSize + 1}`);

                    } else if (direction === 'right' && currentColSize > 1) {

                        $currentCol.removeClass(`col-${currentColSize}`).addClass(`col-${currentColSize - 1}`);
                        $adjacentCol.removeClass(`col-${adjacentColSize}`).addClass(`col-${adjacentColSize + 1}`);
                    }
                }
                adjustToolbar($pflowToolbar, $currentCol);
            }

            function textAlign($paragraph, direction) {
                $paragraph.removeClass('text-center text-start text-end').addClass(`text-${direction}`);
            }

            function getColumnSize($col) {
                let colClass = $col.attr('class').split(' ').find(cls => cls.startsWith('col-'));
                return parseInt(colClass.split('-')[1], 10);
            }
            
            function adjustToolbar($pflowToolbar, $target) {
                let targetOffset = $target.offset();
                let targetWidth = $target.outerWidth();
                let toolbarWidth = $pflowToolbar.outerWidth();

                if (targetOffset.left + targetWidth - toolbarWidth < 0) {
                    $pflowToolbar.addClass('pflow-toolbar-left');
                } else {
                    $pflowToolbar.removeClass('pflow-toolbar-left');
                }
            }

            function removeTarget($target) {
                $('[data-bs-toggle="tooltip"]', $target).tooltip('dispose');
                $('.pflow-toolbar', $target).remove();
                $target.remove();
            }

            function createToolbarItem(iconPath, title, className, eventHandler, eventType = 'click') {
                const $item = $(`
                    <div class="pflow-toolbar-item ${className}" title="${title}" data-bs-toggle="tooltip">
                        <svg viewBox="0 0 24 24">
                            <path d="${iconPath}"></path>
                        </svg>
                    </div>
                `);
            
                if (eventHandler) {
                    $item.on(eventType, eventHandler);
                }
            
                return $item;
            }

            // Add the toolbar
            function addToolbar(target) {
                
                $('.pflow-toolbar', $container).remove();

                let $pflowToolbar = $('<div class="pflow-toolbar"></div>');

                $pflowToolbar.append(
                    createToolbarItem(
                        'M13,6V11H18V7.75L22.25,12L18,16.25V13H13V18H16.25L12,22.25L7.75,18H11V13H6V16.25L1.75,12L6,7.75V11H11V6H7.75L12,1.75L16.25,6H13Z',
                        'Move',
                        'no-touch-actions move',
                        function (e) {
                            e.preventDefault();
        
                            $('body').css('cursor', 'move');
                            startDrag($(target), true);
                            
                            $(document).on('mouseup touchend', function (e) {
                                $('body').css('cursor', '');
                                stopDrag(true);
                            });
                        },
                        'mousedown touchstart'
                    ),
                    createToolbarItem(
                        'M19,21H8V7H19M19,5H8A2,2 0 0,0 6,7V21A2,2 0 0,0 8,23H19A2,2 0 0,0 21,21V7A2,2 0 0,0 19,5M16,1H4A2,2 0 0,0 2,3V17H4V3H16V1Z',
                        'Replicate',
                        'replicate',
                        function () {
                            let $clonedElement = $(target).clone();
        
                            $(target).removeClass('pflow-selected');
                            $(target).removeClass('pflow-over');
                            
                            $('[data-bs-toggle="tooltip"]', $pflowToolbar).tooltip('dispose');
                            $pflowToolbar.remove();
        
                            $(target).after($clonedElement);
        
                            // Redistribute the remaining columns in the row
                            if ($(target).hasClass('col')) {
                                let row = $(target).parent('.row');
                                updateRowCols(row);
                            }
                            
                            setTimeout(function() {
        
                                updateDropZones();
        
                                selectTarget($clonedElement);
        
                                if ($clonedElement.hasClass('pflow-editor')) {
                                    let ckelement = $clonedElement.find('.ckeditor');
                                    initCKEditor(ckelement.get(0));
                                }
                            }, 0);
                        }
                    ),
                    createToolbarItem(
                        'M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z',
                        'Remove',
                        'remove',
                        function () {
                            if ($(target).hasClass('col')) {
                                
                                let row = $(target).parent('.row');
                                removeTarget($(target));
                                // Redistribute the remaining columns in the row
                                updateRowCols(row);
                            } else {
        
                                if($(target).is('.pflow-img') || $(target).is('.pflow-card')) {
                                    
                                    const filePath = $(target).find('img').attr('src');
        
                                    if(filePath !== '') {
        
                                        const data = {
                                            filePath: filePath,
                                            uniqid: pflow_uniqId,
                                            timestamp: pflow_time,
                                            token: pflow_token
                                        };
        
                                        $.ajax({
                                            url: settings.removeUrl,
                                            type: 'POST',
                                            contentType: 'application/json',
                                            data: JSON.stringify(data),
                                            success: function (response) {
                                                if (response.status === 'success') {
                                                    removeTarget($(target));
                                                } else {
                                                    console.error('Error during suppression:', response.error.message);
                                                }
                                            },
                                            error: function (jqXHR, textStatus, errorThrown) {
                                                console.error('Ajax error:', textStatus, errorThrown);
                                            }
                                        });
                                    } else {
                                        removeTarget($(target));
                                    }
                                } else {
                                    removeTarget($(target));
                                }
                            }
                            updateDropZones();
                        }
                    ),
                );
                
                $pflowToolbar.appendTo($(target));

                // Extra features for video
                if ($(target).hasClass('pflow-video')) {
                    let $video = $(target).find('video');
                    let src = $video.find('source').attr('src');
                    
                    $(`<div class="pflow-toolbar-item">
                            <input type="text" name="url" placeholder="Url" value="` + src + `">
                        </div>`).prependTo($pflowToolbar);
                    
                    $pflowToolbar.on('keyup change', '[name="url"]', function() {
                        let newSrc = $(this).val();
                        $video.find('source').attr('src', newSrc);
                        $video[0].load();
                    });
                }

                // Extra features for links
                if ($(target).hasClass('pflow-button')) {
                    let $button = $(target).find('a');
                    let url = $button.attr('href');
                    
                    $(`<div class="pflow-toolbar-item">
                            <input type="text" name="url" placeholder="Url" value="` + url + `">
                        </div>`).prependTo($pflowToolbar);
                    
                    $pflowToolbar.on('keyup change', '[name="url"]', function() {
                        let newUrl = $(this).val();
                        $button.attr('href', newUrl);
                    });
                }

                // Extra features for p and headings
                if ($(target).is('div, p, h1, h2, h3, h4')) {
                    let $paragraph = $(target);

                    createToolbarItem(
                        'M18 5a1 1 0 100-2H2a1 1 0 000 2h16zm0 4a1 1 0 100-2h-8a1 1 0 100 2h8zm1 3a1 1 0 01-1 1H2a1 1 0 110-2h16a1 1 0 011 1zm-1 5a1 1 0 100-2h-8a1 1 0 100 2h8z',
                        'Align right',
                        'text-end',
                        function() {
                            textAlign($paragraph, 'end');
                        }
                    ).prependTo($pflowToolbar);

                    createToolbarItem(
                        'M18 5a1 1 0 100-2H2a1 1 0 000 2h16zm-4 4a1 1 0 100-2H6a1 1 0 100 2h8zm5 3a1 1 0 01-1 1H2a1 1 0 110-2h16a1 1 0 011 1zm-5 5a1 1 0 100-2H6a1 1 0 100 2h8z',
                        'Align center',
                        'text-center',
                        function() {
                            textAlign($paragraph, 'center');
                        }
                    ).prependTo($pflowToolbar);

                    createToolbarItem(
                        'M18 5a1 1 0 100-2H2a1 1 0 000 2h16zm-8 4a1 1 0 100-2H2a1 1 0 100 2h8zm9 3a1 1 0 01-1 1H2a1 1 0 110-2h16a1 1 0 011 1zm-9 5a1 1 0 100-2H2a1 1 0 100 2h8z',
                        'Align left',
                        'text-start',
                        function() {
                            textAlign($paragraph, 'start');
                        }
                    ).prependTo($pflowToolbar);
                }

                // Extra features for cols
                if ($(target).hasClass('col')) {
                    let $col = $(target);
                    
                    let $prevCol = $col.prevAll('.col').first(); 
                    let $nextCol = $col.nextAll('.col').first(); 

                    if ($nextCol.length > 0) {

                        createToolbarItem(
                            'M4,2H2V22H4V13H18.17L12.67,18.5L14.08,19.92L22,12L14.08,4.08L12.67,5.5L18.17,11H4V2Z',
                            'Extend right',
                            'pflow-extend pflow-extend-right',
                            function() {
                                adjustColumnWidth($pflowToolbar, $col, $nextCol, 'right', 'expand');
                            }
                        ).prependTo($pflowToolbar);

                        createToolbarItem(
                            'M11.92,19.92L4,12L11.92,4.08L13.33,5.5L7.83,11H22V13H7.83L13.34,18.5L11.92,19.92M4,12V2H2V22H4V12Z',
                            'Collapse right',
                            'pflow-collapse pflow-collapse-right',
                            function() {
                                adjustColumnWidth($pflowToolbar, $col, $nextCol, 'right', 'collapse');
                            }
                        ).prependTo($pflowToolbar);
                    }

                    if ($prevCol.length > 0) {

                        createToolbarItem(
                            'M12.08,4.08L20,12L12.08,19.92L10.67,18.5L16.17,13H2V11H16.17L10.67,5.5L12.08,4.08M20,12V22H22V2H20V12Z',
                            'Collapse left',
                            'pflow-collapse pflow-collapse-left',
                            function() {
                                adjustColumnWidth($pflowToolbar, $col, $prevCol, 'left', 'collapse');
                            }
                        ).prependTo($pflowToolbar);

                        createToolbarItem(
                            'M20,22H22V2H20V11H5.83L11.33,5.5L9.92,4.08L2,12L9.92,19.92L11.33,18.5L5.83,13H20V22Z',
                            'Extend left',
                            'pflow-extend pflow-extend-left',
                            function() {
                                adjustColumnWidth($pflowToolbar, $col, $prevCol, 'left', 'expand');
                            }
                        ).prependTo($pflowToolbar);
                    }
                }

                $('[data-bs-toggle="tooltip"]', $pflowToolbar).tooltip();

                adjustToolbar($pflowToolbar, $(target));
            }

            // Function to redistribute columns after one is deleted
            function updateRowCols(row) {
                let remainingCols = row.find('.col');
                let totalWidth = 0;
                
                remainingCols.each(function () {
                    let currentClasses = $(this).attr('class').split(' ');
                    let colSizeClass = currentClasses.find(c => c.match(/^col-\d+/));
                    if (colSizeClass) {
                        totalWidth += parseInt(colSizeClass.split('-')[1]);
                    }
                });
                
                if (totalWidth !== 12) {

                    let newColWidth = 12 / remainingCols.length;
                    remainingCols.each(function () {
                        $(this).attr('class', `col col-${newColWidth}`);
                    });
                } else {

                    remainingCols.each(function () {
                        let currentClasses = $(this).attr('class').split(' ');
                        let colSizeClass = currentClasses.find(c => c.match(/^col-\d+/));
                        
                        if (!colSizeClass) {
                            $(this).addClass('col-12');
                        } else {
                            $(this).attr('class', `col ${colSizeClass}`);
                        }
                    });
                }
            
                if (remainingCols.length === 0) {
                    row.remove();
                }
            }
        });
    }
})(jQuery);