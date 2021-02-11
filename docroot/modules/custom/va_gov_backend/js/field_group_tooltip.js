/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal, Tippy) {
  Drupal.behaviors.vaGovTooltip = {
    attach: function attach() {
      Tippy(".tooltip-toggle", {
        content: function content(reference) {
          var title = reference.getAttribute("title");
          reference.removeAttribute("title");
          return title;
        },

        theme: "tippy_popover tippy_popover_center",
        offset: "0, -12"
      });
    }
  };
})(jQuery, window.Drupal, window.tippy);