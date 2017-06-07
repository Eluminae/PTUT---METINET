function prepareInputField() {
    var inputs = $('.ranking-block input');

    inputs.each(function(index, value) {
        $(this).val(inputs.size()-index);
    });
}

$.event.props.push('dataTransfer');

$( document ).ready(function() {
    prepareInputField();

    var i, $this;

    $('.ranking-block').on({
        // on commence le drag
        dragstart: function(e) {
            $this = $(this);
            i = $this.index();
            $(this).css('opacity', '0.5');

            // on garde le texte en mémoire
            e.dataTransfer.setData('html', $this.html());
        },
        // on passe sur un élément draggable
        dragenter: function(e) {
            e.preventDefault();
        },
        // on quitte un élément draggable
        dragleave: function() {

        },
        // déclenché tant qu on a pas lâché l élément
        dragover: function(e) {
            e.preventDefault();
        },
        // On lâche l'élément
        drop: function(e) {
            // si l élément sur lequel on drop n'est pas l'élément de départ
            if (i !== $(this).index()) {
                // on récupère le texte initial
                var data = e.dataTransfer.getData('html');

                // on met le nouveau texte à la place de l ancien et inversement
                $this.html($(this).html());
                $(this).html(data);

                prepareInputField();
            }
        },
        // fin du drag (même sans drop)
        dragend: function() {
            $(this).css('opacity', '1');
        },
    });
});
