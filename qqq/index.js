const quizData = [
  {
    question: "Which language runs in a web browser?",
    options: ["Java", "C", "Python", "JavaScript"],
    answer: "JavaScript"
  },
  {
    question: "What does CSS stand for?",
    options: [
      "Central Style Sheets",
      "Cascading Style Sheets",
      "Cascading Simple Sheets",
      "Cars SUVs Sailboats"
    ],
    answer: "Cascading Style Sheets"
  },
  {
    question: "What does HTML stand for?",
    options: [
      "Hypertext Markup Language",
      "Hyperloop Machine Language",
      "Hyper Tool Multi Language",
      "Home Tool Markup Language"
    ],
    answer: "Hypertext Markup Language"
  },
  {
    question: "What year was JavaScript launched?",
    options: ["1996", "1995", "1994", "None of the above"],
    answer: "1995"
  }
];

let currentQuestion = 0;
let score = 0;

const questionNumber = document.getElementById('question-number');
const questionText = document.getElementById('question-text');
const optionList = document.getElementById('option-list');
const nextBtn = document.getElementById('next-question');
const resultBox = document.getElementById('result');
const scoreText = document.getElementById('score');
const totalQuestions = document.getElementById('total-questions');
const restartBtn = document.getElementById('restart-quiz');
const resultMessage = document.getElementById('result-massage');

totalQuestions.textContent = quizData.length;

function loadQuestion() {
  const current = quizData[currentQuestion];
  questionNumber.textContent = `Question ${currentQuestion + 1} of ${quizData.length}`;
  questionText.textContent = current.question;
  optionList.innerHTML = "";

  current.options.forEach(option => {
    const li = document.createElement('li');
    li.textContent = option;
    li.classList.add('option');
    li.onclick = () => selectOption(li, current.answer);
    optionList.appendChild(li);
  });

  nextBtn.disabled = true;
}

function selectOption(selected, correctAnswer) {
  const options = document.querySelectorAll('.option');
  options.forEach(option => {
    option.classList.remove('selected');
    option.style.pointerEvents = 'none'; // disable after selection
  });
  selected.classList.add('selected');

  if (selected.textContent === correctAnswer) {
    score++;
    selected.style.backgroundColor = "#4CAF50"; // green
  } else {
    selected.style.backgroundColor = "#f44336"; // red
  }

  nextBtn.disabled = false;
}

nextBtn.addEventListener('click', () => {
  currentQuestion++;
  if (currentQuestion < quizData.length) {
    loadQuestion();
  } else {
    showResult();
  }
});

function showResult() {
  document.querySelector('.quiz-content').style.display = 'none';
  resultBox.classList.remove('hidden');
  scoreText.textContent = score;
  resultMessage.textContent = score === quizData.length
    ? "Excellent! 🏆"
    : score >= quizData.length / 2
      ? "Good job! 👍"
      : "Try again! 💡";
}

restartBtn.addEventListener('click', () => {
  currentQuestion = 0;
  score = 0;
  resultBox.classList.add('hidden');
  document.querySelector('.quiz-content').style.display = 'block';
  loadQuestion();
});

loadQuestion();