# Kudos https://github.com/ttroy50/cmake-examples/blob/master/01-basic/B-hello-headers/CMakeLists.txt
cmake_minimum_required(VERSION 4.0)

# Set the project name
project (doofus)

set(SOURCES
    src/Board.cpp
    src/Card.cpp
    src/ChipSet.cpp
    src/Game.cpp
    src/Player.cpp
    src/Util.cpp
    src/main.cpp
)

add_executable(doofus ${SOURCES})

target_include_directories(doofus
    PRIVATE
        ${PROJECT_SOURCE_DIR}/include
)
