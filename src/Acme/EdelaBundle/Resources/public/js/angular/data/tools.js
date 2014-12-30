function calendarTool($scope) {
    $scope.template = '/bundles/acmeedela/partials/tools/calendar-tool.html';
}

function magicBallTool($scope) {
    var answers = [
        'Бесспорно',
        'Предрешено',
        'Никаких сомнений',
        'Определённо да',
        'Можешь быть уверен в этом',
        'Мне кажется — «да»',
        'Вероятнее всего',
        'Хорошие перспективы',
        'Знаки говорят — «да»',
        'Да',
        'Пока не ясно, попробуй снова',
        'Спроси позже',
        'Лучше не рассказывать',
        'Сейчас нельзя предсказать',
        'Сконцентрируйся и спроси опять',
        'Даже не думай',
        'Мой ответ — «нет»',
        'По моим данным — «нет»',
        'Перспективы не очень хорошие',
        'Весьма сомнительно'
    ];
    $scope.newanswer = function () {
        var index = Math.floor(Math.random() * answers.length);
        $scope.answer = answers[index];
    }

    $scope.answer = 'Кликните,<br>чтобы узнать<br>ответ';
    $scope.template = '/bundles/acmeedela/partials/tools/magic-ball-tool.html';
}

function timerusTool($scope, $interval, ngAudio) {
    $scope.template = '/bundles/acmeedela/partials/tools/timerus-tool.html';
    $scope.melodies = [
        {title: 'Аэропорт', file: 'Airport.wav'},
        {title: 'Будильник', file: 'Alarm.wav'},
        {title: 'Будильник 2', file: 'Alarm2.wav'},
        {title: 'Будильник 3', file: 'Alarm3.wav'},
        {title: 'Кукушка', file: 'Bird.wav'},
        {title: 'Петух', file: 'Chicken.wav'},
        {title: 'Полиция', file: 'Cops.wav'},
        {title: 'Сердце', file: 'Heart.wav'},
        {title: 'Металл', file: 'Metal.wav'},
        {title: 'Пип', file: 'Pip.wav'},
        {title: 'Вибрация', file: 'Vibr.wav'}
    ];

    $scope.timer = {};
    var sound = false;
    $scope.changeMelody = function () {
        sound = ngAudio.load("/uploads/timerus/" + $scope.timer.melody);
        sound.play();
    }


    var interval;
    $scope.start = function () {
        if ($scope.timer.running) {
            $interval.cancel(interval);
            $scope.timer.running = false;
            return;
        }
        $scope.timer.running = true;
        var time = Number($scope.timer.sec || 0) + (Number($scope.timer.min || 0) * 60) + (Number($scope.timer.hour || 0) * 3600);
        var startTime = time;
        interval = $interval(function () {
            time--;
            console.log(time);
            $scope.timer.hour = Math.floor(time / 3600);
            $scope.timer.min = Math.floor((time - ($scope.timer.hour * 3600)) / 60);
            $scope.timer.sec = time - ($scope.timer.hour * 3600) - ($scope.timer.min * 60)
            if (time < 1) {
                if (sound) {
                    sound.play();
                }
                if ($scope.timer.loop) {
                    time = startTime;
                } else {
                    $interval.cancel(interval);
                    $scope.timer.running = false;
                }
            }

        }, 1000);
    }
}

function dayScoreTool($scope) {
    $scope.options = {
        1: 'Хуже быть не может',
        2: 'Очень плохо',
        3: 'Плохо',
        4: 'Так себе',
        5: 'Средне',
        6: 'Нормально',
        7: 'Хорошо',
        8: 'Очень хорошо',
        9: 'Отлично',
        10: 'Безумно хорошо'
    }
    $scope.template = '/bundles/acmeedela/partials/tools/day-score-tool.html';
}