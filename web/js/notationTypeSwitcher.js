$(document).ready(function() {
    $('.mark-type-switcher').each(function() {
        $markTypeSwitcher = $(this)
        var $numberRadioSelector = $markTypeSwitcher.find('input:radio[value=2]');
        var $rankRadioSelector = $markTypeSwitcher.find('input:radio[value=1]');

        $numberRadioSelector.on('click', function() {
            $markTypeSwitcher.find('.mark-type-number').show()
        })

        $rankRadioSelector.on('click', function() {
            $markTypeSwitcher.find('.mark-type-number').hide()
            $markTypeSwitcher.find('.mark-type-number input:text').val("0")
        })
    });
});
