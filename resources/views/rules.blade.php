@extends('layouts.app')

@section('title', 'Правила сайта | Площадка для традиционных знакомств в Казахстане')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cabinet.css') }}?v={{ filemtime(public_path('css/cabinet.css')) }}">

<style>
.rules-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.rules-card {
    background: white;
    border-radius: 16px;
    padding: 3rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.rules-card h1 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.5rem;
    text-align: center;
}

.rules-subtitle {
    text-align: center;
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 2.5rem;
    line-height: 1.6;
}

.rules-section {
    margin-bottom: 2rem;
}

.rules-section h2 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.rules-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.rules-section ul li {
    padding: 0.6rem 0 0.6rem 1.5rem;
    position: relative;
    color: #475569;
    line-height: 1.7;
    font-size: 0.95rem;
}

.rules-section ul li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 1rem;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #3b82f6;
}

.warning-block {
    background: #fef2f2;
    border: 2px solid #fecaca;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 2rem 0;
}

.warning-block h3 {
    color: #991b1b;
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.warning-block ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.warning-block ul li {
    padding: 0.4rem 0 0.4rem 1.5rem;
    position: relative;
    color: #991b1b;
    line-height: 1.7;
    font-size: 0.9rem;
}

.warning-block ul li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.85rem;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #ef4444;
}

.disclaimer-block {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.disclaimer-block h3 {
    color: #1a202c;
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.disclaimer-block p {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.7;
    margin-bottom: 0.5rem;
}

.disclaimer-block p:last-child {
    margin-bottom: 0;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s ease;
    margin-bottom: 2rem;
}

.back-button:hover {
    background: #e2e8f0;
    color: #1a202c;
    text-decoration: none;
}

.back-button svg {
    width: 16px;
    height: 16px;
    fill: currentColor;
}

@media (max-width: 768px) {
    .rules-container {
        padding: 0;
    }

    .rules-card {
        padding: 1.75rem;
    }

    .rules-card h1 {
        font-size: 1.4rem;
    }
}
</style>

<div class="rules-container">

    <a href="{{ route('home') }}" class="back-button">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
        </svg>
        На главную
    </a>

    <div class="rules-card">

        <h1>Правила сайта</h1>
        <div class="rules-subtitle">
            Наш сайт — площадка для традиционных знакомств.<br>
            Пользуясь сайтом, вы соглашаетесь с данными правилами.<br>
            Нарушение правил влечёт блокировку аккаунта без возврата средств.
        </div>

        <!-- Общие правила -->
        <div class="rules-section">
            <h2>1. Общие правила</h2>
            <ul>
                <li>Сайт предназначен исключительно для лиц, достигших 18 лет.</li>
                <li>Сайт является площадкой для традиционных знакомств. Цель — помочь людям найти серьёзные, взаимовыгодные отношения.</li>
                <li>Администрация не несёт ответственности за действия пользователей за пределами сайта.</li>
                <li>Пользуясь сайтом, вы подтверждаете, что действуете добровольно и осознанно.</li>
            </ul>
        </div>

        <!-- Правила размещения объявлений -->
        <div class="rules-section">
            <h2>2. Правила размещения объявлений</h2>
            <ul>
                <li>Объявления должны быть составлены грамотно и уважительно.</li>
                <li><strong>Запрещены</strong> объявления сексуального характера, содержащие пошлость, вульгарные выражения или намёки на интимные услуги.</li>
                <li>Запрещено размещать фотографии откровенного или порнографического содержания.</li>
                <li>Запрещено указывать заведомо ложные данные о себе (возраст, имя, город, фотографии чужих людей).</li>
                <li>Одному пользователю разрешено иметь не более 3 активных объявлений.</li>
                <li>Объявления, нарушающие правила, удаляются без предупреждения.</li>
            </ul>
        </div>

        <!-- Запрещено на сайте -->
        <div class="rules-section">
            <h2>3. Строго запрещено</h2>
            <ul>
                <li>Оскорбления, угрозы, шантаж и любые формы давления на других пользователей.</li>
                <li><strong>Поиск, предложение, реклама и оказание любых интимных услуг за деньги или иное вознаграждение СТРОГО ЗАПРЕЩЕНЫ.</strong> Сайт — площадка для знакомств, а не для эскорта, проституции или сводничества. Запрещены упоминания «спонсора», «содержанки», цен за встречу и любых платных интимных услуг.</li>
                <li>Занятие проституцией, вовлечение в неё и сводничество преследуются по законодательству Республики Казахстан. Аккаунты с такими объявлениями или сообщениями блокируются, данные могут быть переданы правоохранительным органам.</li>
                <li>Мошенничество: вымогательство денег, обман, создание фейковых анкет.</li>
                <li>Распространение персональных данных третьих лиц без их согласия.</li>
                <li>Реклама сторонних сайтов, товаров и услуг.</li>
                <li>Создание нескольких аккаунтов одним лицом с целью обхода блокировки.</li>
                <li><strong>Пропаганда, реклама, продажа и распространение наркотических средств, психотропных веществ и их аналогов.</strong> Любое упоминание запрещённых веществ в объявлениях или переписке ведёт к немедленной блокировке и передаче данных в правоохранительные органы.</li>
                <li>Продажа и реклама оружия, поддельных документов, а также любых товаров и услуг, запрещённых законодательством Республики Казахстан.</li>
            </ul>
        </div>

        <!-- Правила переписки -->
        <div class="rules-section">
            <h2>4. Правила переписки</h2>
            <ul>
                <li>Общение должно быть вежливым и уважительным.</li>
                <li>Запрещена рассылка одинаковых сообщений (спам).</li>
                <li>Запрещены сообщения сексуального и оскорбительного характера.</li>
                <li>Если пользователь не отвечает — не стоит писать повторно. Навязчивость может привести к блокировке.</li>
            </ul>
        </div>

        <!-- Ответственность -->
        <div class="warning-block">
            <h3>Ответственность по законодательству Республики Казахстан</h3>
            <ul>
                <li><strong>Статья 190 УК РК — Мошенничество.</strong> Хищение чужого имущества путём обмана или злоупотребления доверием наказывается штрафом, исправительными работами или лишением свободы на срок до 10 лет.</li>
                <li><strong>Статья 147 УК РК — Нарушение неприкосновенности частной жизни.</strong> Незаконный сбор и распространение персональных данных влечёт уголовную ответственность.</li>
                <li><strong>Статья 385 УК РК — Подделка документов.</strong> Использование поддельных документов и данных наказывается штрафом до 160 МРП или лишением свободы до 4 лет.</li>
                <li><strong>Статья 297 УК РК — Незаконный оборот наркотических средств.</strong> Сбыт, пропаганда и реклама наркотиков наказывается лишением свободы на срок от 3 до 15 лет.</li>
                <li>Администрация сайта оставляет за собой право передавать данные пользователей правоохранительным органам РК по официальному запросу.</li>
            </ul>
        </div>

        <!-- Дисклеймер -->
        <div class="disclaimer-block">
            <h3>Дисклеймер</h3>
            <p>Данный сайт является площадкой для традиционных знакомств и не оказывает, не организует и не рекламирует никаких услуг интимного характера. Администрация не организует встречи и не является посредником в отношениях между пользователями.</p>
            <p><strong>Сайт не предназначен для поиска или предложения платных интимных услуг.</strong> Любые подобные действия пользователя являются нарушением настоящих правил и законодательства Республики Казахстан; всю ответственность за них несёт сам пользователь. Администрация активно модерирует объявления, анкеты и переписку и удаляет подобный контент.</p>
            <p>Все пользователи действуют самостоятельно и несут полную ответственность за свои действия в соответствии с законодательством Республики Казахстан. Администрация не гарантирует достоверность информации, указанной в профилях пользователей.</p>
            <p>Используя данный сайт, вы подтверждаете, что вам исполнилось 18 лет и вы ознакомлены с настоящими правилами.</p>
        </div>

    </div>

</div>

@endsection
