### Дополнительные ресурсы:
- [Диаграммы](https://drive.google.com/file/d/1sB6pk3tqcWiv9Ni-wBaBfj81yAI6Ltxu/view?usp=sharing)
- [ТЗ (список задач)](https://docs.google.com/document/d/1PN9QPVHBz-4sFCNiXN5WwIn0ROAEz-mf_nbeKo81Xn0/edit?usp=sharing)

### Как развернуть проект:
- В директории deploy скопировать файл .env.example в .env и задать переменные
- В директории project сделать то же самое
- Выплнить следующие команды из консоли
  - cd deploy
  - docker-compose up -d
  - docker exec -it app bash
  - composer install
  - php artisan key:generate
  - php artisan migrate
  - php artisan db:seed

Приложение откроется по адресу http://localhost

