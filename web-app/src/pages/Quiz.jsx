import { useState, useEffect } from 'react'
import { useLocation } from 'react-router'

function Quiz() {
  const [quiz, setQuiz] = useState(null)
  const [currentQuestion, setCurrentQuestion] = useState(0)
  const [selectedAnswers, setSelectedAnswers] = useState({})
  const location = useLocation()
  const { secretCode } = location.state || {}

  useEffect(() => {
    const fetchQuiz = async () => {
      try {
        const response = await fetch(
          `http://localhost:8000/api/quiz/${secretCode}`
        )
        const data = await response.json()
        setQuiz(data)
      } catch (error) {
        console.error('Error fetching quiz:', error)
      }
    }

    if (secretCode) {
      fetchQuiz()
    }
  }, [secretCode])

  if (!quiz) {
    return (
      <div className="flex min-h-screen items-center justify-center">
        <div className="text-xl">Loading quiz...</div>
      </div>
    )
  }

  const handleAnswerSelect = (questionId, optionId) => {
    setSelectedAnswers({
      ...selectedAnswers,
      [questionId]: optionId,
    })
  }

  const currentQuestionData = quiz.questions[currentQuestion]

  return (
    <div className="mx-auto max-w-3xl p-6">
      <h1 className="mb-6 text-center text-2xl font-bold">{quiz.title}</h1>
      <div className="rounded-lg bg-white p-6 shadow-lg">
        <div className="mb-4">
          <span className="text-sm text-gray-500">
            Question {currentQuestion + 1} of {quiz.questions.length}
          </span>
        </div>
        <h2 className="mb-4 text-xl font-semibold">
          {currentQuestionData.question_text}
        </h2>
        <div className="space-y-3">
          {currentQuestionData.options.map((option) => (
            <div
              key={option.option_id}
              className="flex items-center space-x-3"
            >
              <input
                type="radio"
                id={`option-${option.option_id}`}
                name={`question-${currentQuestionData.question_id}`}
                value={option.option_id}
                checked={
                  selectedAnswers[currentQuestionData.question_id] ===
                  option.option_id
                }
                onChange={() =>
                  handleAnswerSelect(
                    currentQuestionData.question_id,
                    option.option_id
                  )
                }
                className="h-4 w-4 text-blue-600"
              />
              <label
                htmlFor={`option-${option.option_id}`}
                className="text-gray-700"
              >
                {option.option_text}
              </label>
            </div>
          ))}
        </div>
        <div className="mt-6 flex justify-between">
          <button
            onClick={() => setCurrentQuestion(currentQuestion - 1)}
            disabled={currentQuestion === 0}
            className="rounded bg-gray-500 px-4 py-2 text-white disabled:opacity-50"
          >
            Previous
          </button>
          <button
            onClick={() => setCurrentQuestion(currentQuestion + 1)}
            disabled={currentQuestion === quiz.questions.length - 1}
            className="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  )
}

export default Quiz