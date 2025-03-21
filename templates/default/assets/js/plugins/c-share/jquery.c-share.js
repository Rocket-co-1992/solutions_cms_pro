/*!
 * jquery.c-share.js v1.0.4
 * https://github.com/ycs77/jquery-plugin-c-share
 *
 * Copyright 2019 Lucas, Yang
 * Released under the MIT license
 *
 * Date: 2019-03-29T08:56:33.044Z
 */

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (factory());
}(this, (function () { 'use strict';

  if ($.fn) {

    $.fn.cShare = function (options) {
      var _this = this;

      var defaults = {
        description: '',
        show_buttons: ['fb'],
        data: {
          fb: {
            fa: 'fa-brands fa-facebook-f',
            name: 'Fb',
            href: function href(url) {
              return 'https://www.facebook.com/sharer.php?u=' + url;
            },
            show: true
          },
          line: {
            fa: 'fa-brands fa-line fa-2x',
            name: 'Line',
            href: function href(url) {
              return 'https://lineit.line.me/share/ui?url=' + url;
            },
            show: true,
            hideWrapper: true
          },
          plurk: {
            fa: 'fa-plurk',
            name: 'Plurk',
            href: function href(url, description) {
              return 'http://www.plurk.com/?qualifier=shares&status=' + description + ' ' + url;
            },
            show: false
          },
          twitter: {
            fa: 'fa-brands fa-x-twitter',
            name: 'Twitter',
            href: function href(url, description) {
              return 'https://twitter.com/intent/tweet?original_referer=' + url + '&url=' + url + '&text=' + description;
            },
            show: false
          },
          tumblr: {
            fa: 'fa-brands fa-tumblr',
            name: 'Tumblr',
            href: function href(url, description) {
              return 'http://www.tumblr.com/share/link?name=' + description + ' ' + url + '&url=' + url;
            },
            show: false
          },
          pinterest: {
            fa: 'fa-brands fa-pinterest-p',
            name: 'Pinterest',
            href: function href(url, description) {
              return 'http://pinterest.com/pin/create/button/?url=' + url + '&description=' + description + ' ' + url;
            },
            show: false
          },
          email: {
            fa: 'fa-solid fa-envelope',
            name: 'E-mail',
            href: function href(url, description) {
              return 'mailto:?subject=' + description + '&body=' + description + ' ' + url;
            },
            show: false
          }
        },
        spacing: 6
      };

      var href = location.href.replace(/#\w/, '');
      var mobile = navigator.userAgent.match(/(mobile|android|pad)/i);

      var settings = $.extend({}, defaults, options);
      if (options) {
        settings.data = $.extend({}, defaults.data, options.data);
      }

      settings.show_buttons.forEach(function (shareName) {

        var item = settings.data[shareName];

        // Create button element
        _this.append('\n        <a href="' + item.href.call(null, href, settings.description) + '" title="\u5206\u4EAB\u5230 ' + item.name + '" target="_blank" data-icon="' + shareName + '">\n          <span class="fa-stack">\n            ' + (!item.hideWrapper ? '<i class="fas fa-circle fa-stack-2x"></i>' : '') + '\n            <i class="' + item.fa + ' fa-stack-1x"></i>\n          </span>\n        </a>\n      ');
      });

      this.find('.fa-plurk').text('P');

      // Bind link click event
      this.find('a').click(function (e) {
        if (!mobile) {
          e.preventDefault();
          window.open($(this).attr('href'), '_blank', 'height=600,width=500');
        }
      });

      // Add CSS
      this.children('a').css({
        'display': 'inline-block',
        'margin': 'auto ' + Number(settings.spacing) / 2 + 'px',
        'text-decoration': 'none',
        '-webkit-transition': 'all .2s',
        '-moz-transition': 'all .2s',
        'transition': 'all .2s'
      });
      if (!mobile) {
        this.children('a').hover(function () {
          $(this).css({
            '-webkit-transform': 'translateY(-4px)',
            '-ms-transform': 'translateY(-4px)',
            'transform': 'translateY(-4px)'
          });
        }, function () {
          $(this).css({
            '-webkit-transform': 'translateY(0px)',
            '-ms-transform': 'translateY(0px)',
            'transform': 'translateY(0px)'
          });
        });
      }

      // Set color
      //this.find('.fa-stack-1x').css('color', '#ffffff');
      this.find('[data-icon="fb"] i').css('color', '#3B5998');
      this.find('[data-icon="line"] i').css('color', '#00c300');
      this.find('[data-icon="plurk"] .fa-stack-2x').css('color', '#cf682f');
      this.find('[data-icon="plurk"] .fa-plurk').css({
        'font-family': 'arial',
        'font-style': 'normal',
        'font-weight': 'bold'
      });
      this.find('[data-icon="twitter"] i').css('color', '#181B20');
      this.find('[data-icon="tumblr"] i').css('color', '#35465d');
      this.find('[data-icon="pinterest"] i').css('color', '#C71E27');
      this.find('[data-icon="email"] i').css('color', '#939598');

      return this;
    };
  }

})));
