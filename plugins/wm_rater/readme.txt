-Микроразметка
- rater_info_bottom (инфо сколько голосовало и средяя оценка)
подключение хук в info-bottom.php echo mso_hook('info_bottom_end');//rater
css class="rater_info_bottom"

- rater_content_end (основная инфо панель с голосовалкой)
подключение хук в page.php echo echo mso_hook('wm_page_end');//плагин rater
css class="rater_body"


