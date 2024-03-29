from time import sleep
from dataclasses import dataclass
from app.shared.infrastructure.facades.dom import Dom


@dataclass
class Element:
    __dom: Dom

    def __int__(self, dom: Dom):
        self.__dom = dom

    def set_value(self, element_id: str, value: str) -> None:
        if not self.__dom:
            return None

        input_element = self.__dom.find_by_id(element_id)
        input_element.send_keys(value)
        sleep(0.2)

    def set_value_by_name(self, element_name: str, value: str) -> None:
        if not self.__dom:
            return None

        input_element = self.__dom.find_by_name(element_name)
        input_element.send_keys(value)
        sleep(0.2)

    @staticmethod
    def set_value_in_input(input_element: object, value: str) -> None:
        input_element.send_keys(value)
        sleep(0.2)

    def set_value_by_xpath(self, xpath: str, value: str) -> None:
        input_element = self.__dom.find_by_xpath(xpath)
        input_element.send_keys(value)
        sleep(0.2)
