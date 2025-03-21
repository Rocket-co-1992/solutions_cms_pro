async function generateToken(uniqid, timestamp) {
    const msg = 'sessid_' + uniqid + timestamp;
    const msgBuffer = new TextEncoder().encode(msg);
    const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    return hashHex;
}

import {
	ClassicEditor,
	AccessibilityHelp,
	Alignment,
	Autoformat,
	AutoImage,
	AutoLink,
	Autosave,
	BalloonToolbar,
	BlockQuote,
	Bold,
	Code,
	CodeBlock,
	Essentials,
	FindAndReplace,
	FontBackgroundColor,
	FontColor,
	FontFamily,
	FontSize,
	FullPage,
	GeneralHtmlSupport,
	Heading,
	Highlight,
	HorizontalLine,
	HtmlComment,
	HtmlEmbed,
	ImageBlock,
	ImageCaption,
	ImageInline,
	ImageInsert,
	ImageInsertViaUrl,
	ImageResize,
	ImageStyle,
	ImageTextAlternative,
	ImageToolbar,
	ImageUpload,
	Indent,
	IndentBlock,
	Italic,
	Link,
	LinkImage,
	List,
	ListProperties,
	MediaEmbed,
	Paragraph,
	PasteFromOffice,
	RemoveFormat,
	SelectAll,
	ShowBlocks,
	SimpleUploadAdapter,
	SourceEditing,
	SpecialCharacters,
	SpecialCharactersArrows,
	SpecialCharactersCurrency,
	SpecialCharactersEssentials,
	SpecialCharactersLatin,
	SpecialCharactersMathematical,
	SpecialCharactersText,
	Strikethrough,
	Style,
	Subscript,
	Superscript,
	Table,
	TableCaption,
	TableCellProperties,
	TableColumnResize,
	TableProperties,
	TableToolbar,
	TextPartLanguage,
	TextTransformation,
	TodoList,
	Underline,
	Undo
} from 'ckeditor5';

import translations from 'ckeditor5/translations/fr.js';

const editorConfig = {
	toolbar: {
        items: [
            'undo', 'redo','|',
            'heading', 'style','|',
            'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|','alignment', '|',
            'fontSize', 'fontColor', '|',
            'bulletedList', 'numberedList', 'outdent', 'indent', 
            '-',
            'link', 'horizontalLine', 'insertTable', 'blockQuote', 'specialCharacters', '|',
            'imageInsert', 'mediaEmbed', '|',
            'showBlocks', 'sourceEditing'
        ],
        shouldNotGroupWhenFull: true
    },
	plugins: [
		AccessibilityHelp,
		Alignment,
		Autoformat,
		AutoImage,
		AutoLink,
		Autosave,
		BalloonToolbar,
		BlockQuote,
		Bold,
		Code,
		CodeBlock,
		Essentials,
		FindAndReplace,
		FontBackgroundColor,
		FontColor,
		FontFamily,
		FontSize,
		FullPage,
		GeneralHtmlSupport,
		Heading,
		Highlight,
		HorizontalLine,
		HtmlComment,
		HtmlEmbed,
		ImageBlock,
		ImageCaption,
		ImageInline,
		ImageInsert,
		ImageInsertViaUrl,
		ImageResize,
		ImageStyle,
		ImageTextAlternative,
		ImageToolbar,
		ImageUpload,
		Indent,
		IndentBlock,
		Italic,
		Link,
		LinkImage,
		List,
		ListProperties,
		MediaEmbed,
		Paragraph,
		PasteFromOffice,
		RemoveFormat,
		SelectAll,
		ShowBlocks,
		SimpleUploadAdapter,
		SourceEditing,
		SpecialCharacters,
		SpecialCharactersArrows,
		SpecialCharactersCurrency,
		SpecialCharactersEssentials,
		SpecialCharactersLatin,
		SpecialCharactersMathematical,
		SpecialCharactersText,
		Strikethrough,
		Style,
		Subscript,
		Superscript,
		Table,
		TableCaption,
		TableCellProperties,
		TableColumnResize,
		TableProperties,
		TableToolbar,
		TextPartLanguage,
		TextTransformation,
		TodoList,
		Underline,
		Undo
	],
	balloonToolbar: ['bold', 'italic', '|', 'link', 'insertImage', '|', 'bulletedList', 'numberedList'],
	fontFamily: {
		supportAllValues: true
	},
	fontSize: {
		options: [10, 12, 14, 'default', 18, 20, 22],
		supportAllValues: true
	},
	heading: {
		options: [
            { model: 'paragraph', title: 'Paragraphe', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Titre 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Titre 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Titre 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Titre 4', class: 'ck-heading_heading4' }
		]
	},
	htmlSupport: {
		allow: [
			{
				name: /^.*$/,
				styles: true,
				attributes: true,
				classes: true
			}
		],
		allowEmpty: [ 'i', 'span' ]
	},
    link: {
		defaultProtocol: 'https://',
		decorators: {
			openInNewTab: {
				mode: 'manual',
				label: 'Ouvrir dans un nouvel onglet',
				attributes: {
					target: '_blank'
				}
			},
			isLinkButton: {
				mode: 'manual',
				label: 'Mon lien est un bouton',
				attributes: {
					class: 'btn btn-primary'
				}
			}
		}
	},
	simpleUpload: {

		// Enable the XMLHttpRequest.withCredentials property.
		withCredentials: true,

		// Headers sent along with the XMLHttpRequest to the upload server.
		headers: {
			'X-CSRF-TOKEN': 'CSRF-Token',
			Authorization: 'Bearer <JSON Web Token>'
		}
	},
	image: {
		toolbar: [
			'toggleImageCaption',
			'imageTextAlternative',
			'|',
			'imageStyle:inline',
			'imageStyle:wrapText',
			'imageStyle:breakText',
			'|',
			'resizeImage'
		]
	},
	language: 'fr',
	list: {
		properties: {
			styles: true,
			startIndex: true,
			reversed: true
		}
	},
	placeholder: '...',
    style: {
        definitions: [
            { name: 'Paragraphe focus', element: 'p', classes: ['lead'] },
            { name: 'Multilignes focus', element: 'div', classes: ['hotBox'] }
        ]
    },
	table: {
		contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
	},
	translations: [translations]
};

$(function(){
    'use strict';
    if($('textarea[data-editor="1"]').length){
        $('textarea[data-editor="1"]').each(function(index, elm){
            ClassicEditor.create(document.querySelector('#'+elm.id), editorConfig)

			.then(editor => {

				editor.plugins.get('FileRepository').createUploadAdapter = loader => {
					return {
						upload: async () => {
							const file = await loader.file;
							const timestamp = Math.round(Date.now() / 1000);
							const token = await generateToken(uniqId, timestamp);
		
							const data = new FormData();
							data.append('upload', file);
							data.append('uniqid', uniqId);
							data.append('timestamp', timestamp);
							data.append('token', token);
		
							const response = await fetch('xhr/uploader', {
								method: 'POST',
								body: data,
								headers: {
									'X-CSRF-TOKEN': 'CSRF-Token',
									Authorization: 'Bearer <JSON Web Token>',
        							'X-Requested-With': 'XMLHttpRequest'
								}
							});
		
							const result = await response.json();
							if (result.url) {
								return { default: result.url };
							} else {
								return Promise.reject(result.error.message);
							}
						}
					};
				};

				editor.model.document.on('change:data', () => {
					editor.editing.view.change(writer => {
						// Get all image elements in the content
						const images = Array.from(editor.editing.view.document.getRoot().getChildren())
							.flatMap(element => Array.from(element.getChildren()))
							.filter(child => child.is('element', 'img'));
				
						// Add 'img-fluid' class to each image element
						images.forEach(image => {
							if (!image.hasClass('img-fluid')) {
								writer.addClass('img-fluid', image);
							}
						});
					});
				});

				let previousImages = new Set(
					Array.from(new DOMParser().parseFromString(editor.getData(), 'text/html')
						.querySelectorAll('img'))
						.map(img => img.getAttribute('src'))
						.filter(src => src !== null)
				);
		
				editor.model.document.on('change:data', () => {
					const currentImages = new Set(
						Array.from(new DOMParser().parseFromString(editor.getData(), 'text/html')
							.querySelectorAll('img'))
							.map(img => img.getAttribute('src'))
							.filter(src => src !== null)
					);
		
					const deletedImages = [...previousImages].filter(src => !currentImages.has(src));
		
					deletedImages.forEach(src => {

						fetch('xhr/remover', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
        						'X-Requested-With': 'XMLHttpRequest'
							},
							body: JSON.stringify({ filePath: src })
						})
						.then(response => response.json())
						.then(result => {
							if(result.status !== 'success') {
								console.error('File deletion failed: ', result.error.message);
							}
						})
						.catch(error => console.error('File deletion error: ', error));
					});
		
					previousImages = currentImages;
				});
			})
			.catch(error => console.error(error));
        });
    }
});