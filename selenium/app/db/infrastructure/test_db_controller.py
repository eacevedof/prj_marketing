from app.db.application.test_db_service import get_users


def __invoke() -> None:
    get_users()


__invoke()
