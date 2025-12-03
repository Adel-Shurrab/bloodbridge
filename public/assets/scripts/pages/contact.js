// FAQ Accordion Logic
const faqQuestions = document.querySelectorAll(".faq-question");
faqQuestions.forEach((question) => {
    question.addEventListener("click", () => {
        const item = question.parentElement;
        const answer = item.querySelector(".faq-answer");

        item.classList.toggle("active");

        if (item.classList.contains("active")) {
            answer.style.maxHeight = answer.scrollHeight + "px";
        } else {
            answer.style.maxHeight = "0";
        }
    });
});